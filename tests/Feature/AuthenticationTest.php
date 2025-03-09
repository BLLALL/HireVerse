<?php

use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\getJson;

// it("gets the correct status code for unauthenticated users", function(): void {
//     getJson('api/jobs')->assertStatus(Response::HTTP_UNAUTHORIZED);
//     getJson('api/companies')->assertStatus(Response::HTTP_UNAUTHORIZED);
// });

// it("gets the correct status code for authenticated users", function(): void {
//     loginApplicant()->getJson('api/jobs')->assertStatus(Response::HTTP_OK);
//     loginApplicant()->getJson('api/companies')->assertStatus(Response::HTTP_OK);
// });
