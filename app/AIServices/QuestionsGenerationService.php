<?php

namespace App\AIServices;

use App\Models\Job;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuestionsGenerationService
{
    protected $client;

    protected $apiKey;

    protected $historyFile;

    protected $questionHistory = [];

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');

        $this->client = new Client([
            'base_uri' => 'https://api.groq.com/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
        ]);

    }

    public function generateQuestions(Job $job, int $questionsPerSkill, int $maxAttempts = 3)
    {
        $this->historyFile = "questions_history/job_{$job->id}.json";

        if (Storage::fileExists($this->historyFile)) {
            $this->questionHistory = json_decode(Storage::get($this->historyFile), true);
        } else {
            Storage::put($this->historyFile, json_encode([]));
        }

        $allQuestions = [];

        $skills = $job->skills_titles;
        $jobTitle = $job->title;

        foreach ($skills as $skill) {
            $this->questionHistory[$jobTitle][$skill] = $this->questionHistory[$jobTitle][$skill] ?? [];
            $history = &$this->questionHistory[$jobTitle][$skill];

            $attempt = 0;
            $questionsGenerated = 0;

            while ($questionsGenerated < $questionsPerSkill && $attempt < $maxAttempts) {
                $attempt++;

                $prompt = $this->getPrompt($jobTitle, $skill, $history, $questionsPerSkill - $questionsGenerated);

                try {
                    $response = $this->client->post('openai/v1/chat/completions', [
                        'json' => [
                            'model' => 'meta-llama/llama-4-scout-17b-16e-instruct',
                            'messages' => [
                                [
                                    'role' => 'system',
                                    'content' => 'You generate unique technical questions in valid JSON format. Your primary goal is to create questions that cover new aspects of the skill not addressed in the provided history.',
                                ],
                                [
                                    'role' => 'user',
                                    'content' => $prompt,
                                ],
                            ],
                            'temperature' => 0.7,
                            'max_tokens' => 4096,
                            'frequency_penalty' => 1.0,
                            'presence_penalty' => 1.0,
                            'response_format' => ['type' => 'json_object'],
                        ],
                    ]);

                    $body = json_decode($response->getBody()->getContents(), true);
                    $newQuestions = json_decode($body['choices'][0]['message']['content'], true)['questions'] ?? [];

                    $uniqueQuestions = [];
                    foreach ($newQuestions as $q) {
                        $questionText = $q['question'] ?? '';
                        if ($questionText && ! $this->isQuestionSimilar($questionText, $history)) {
                            $uniqueQuestions[] = $q;
                            $history[] = $questionText;
                            $questionsGenerated++;

                            $allQuestions[] = [
                                // 'job_title' => $jobTitle,
                                // 'skill' => $skill,
                                'question' => $q['question'] ?? 'N/A',
                                'difficulty' => $q['difficulty'] ?? 'N/A',
                                'expected_keywords' => implode(', ', $q['keywords'] ?? []),
                                'assessment_criteria' => $q['assessment_criteria'] ?? 'N/A',
                            ];

                            if ($questionsGenerated >= $questionsPerSkill) {
                                break;
                            }
                        }
                    }

                } catch (\Exception $e) {
                    Log::warning("Groq API error for {$jobTitle} - {$skill}: {$e->getMessage()}");
                }
            }

            if ($questionsGenerated < $questionsPerSkill) {
                Log::info("Only generated {$questionsGenerated}/{$questionsPerSkill} for {$jobTitle} - {$skill}");
            }
        }

        Storage::put($this->historyFile, json_encode($this->questionHistory, JSON_PRETTY_PRINT));

        return $allQuestions;
    }

    protected function isQuestionSimilar(string $new, array $history, float $threshold = 0.8): bool
    {
        foreach ($history as $existing) {
            similar_text(strtolower($existing), strtolower($new), $percent);
            if ($percent > $threshold * 100) {
                return true;
            }
        }

        return false;
    }

    protected function getPrompt(string $jobTitle, string $skill, array $history, int $remaining): string
    {
        $historyText = $history ? implode("\n", array_map(fn ($q) => "- {$q}", $history)) : '- None yet.';

        return <<<PROMPT
Generate {$remaining} *strictly new* technical interview questions for the skill **{$skill}** (Job Title: **{$jobTitle}**). Questions must be designed for verbal responses with clearly bounded scopes and must not repeat or resemble any previous questions.

**PREVIOUSLY ASKED QUESTIONS (DO NOT REPEAT OR PARAPHRASE):**
{$historyText}

**QUESTION DESIGN RULES:**
1. **Clarity**: Questions must be concise and unambiguous.
2. **Structure**: Answers should be objectively assessable and not open-ended.
3. **Difficulty**: Assign easy, medium, or hard based on depth of knowledge.
4. **Novelty**: Cover new subtopics, contexts, or angles not seen before.

**PREFERRED QUESTION TYPES (VERBAL, STRUCTURED):**
- **Scenario-Based**: 'Given [X constraints], what factors affect your choice in [Y]?'
- **Comparison**: 'Compare X and Y in the context of Z. When is each preferable?'
- **Process-Oriented**: 'List and justify the key steps to achieve [specific outcome].'
- **Diagnosis/Optimization**: 'What signs point to [problem], and how would you resolve it?'

**AVOID:**
- Generic prompts like 'Describe your approach to...'
- Open-ended or subjective questions
- Any reused, reworded, or similar questions from the history above

**OUTPUT FORMAT (STRICT JSON):**
{
"questions": [
    {
    "question": "[Well-structured, bounded question]",
    "difficulty": "easy | medium | hard",
    "keywords": ["concept1", "concept2", "concept3"],
    "assessment_criteria": "Specific, measurable guidelines for evaluating responses. Include how each keyword will be assessed."
    }
]
}
PROMPT;
    }
}
