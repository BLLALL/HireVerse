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
    protected const MODEL_NAME = "meta-llama/llama-4-scout-17b-16e-instruct";
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
                            "model" => self::MODEL_NAME,
                            "messages" => [
                                [
                                    "role" => "system",
                                    "content" => "You are an expert technical interviewer creating unique, structured, AI-evaluable interview questions in valid JSON."
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
                                'expected_keywords' => $q['keywords'] ?? [],
                                'expected_answer_components' => $q['expected_answer_components'] ?? [],
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
        Generate {$remaining} strictly new, high-quality technical interview questions for a {$jobTitle} focusing on {$skill}.
        Questions must be designed for verbal responses with objectively verifiable answers.

        PREVIOUSLY GENERATED QUESTIONS (DO NOT REPEAT OR PARAPHRASE):
        {$historyText}

        REQUIREMENTS:
        - Questions must have objectively correct, verifiable answers.
        - Provide a list of the essential, concrete, factual components of the correct answer in 'expected_answer_components'. The components should be short sentences that represent the correct answer, not criterion or what the answer should include.
        - Avoid subjective or open-ended questions (e.g., 'What do you think of...', 'Describe how to...').

        OUTPUT (Strict JSON format):
        {{
        "questions": [
            {{
            "question": "[Concise, verifiable question]",
            "difficulty": "Easy | Medium | Hard",
            "keywords": ["concept1", "concept2"],
            "expected_answer_components": ["component1", "component2", "component3", ...],
            "assessment_criteria": "[Brief guidelines for evaluation]"
            }}
        ]
        }}
        PROMPT;
    }
}
