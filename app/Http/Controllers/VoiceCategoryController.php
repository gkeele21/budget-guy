<?php

namespace App\Http\Controllers;

use App\Services\VoiceCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceCategoryController extends Controller
{
    public function __construct(
        private VoiceCategoryService $voiceService
    ) {}

    public function parse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transcript' => 'required|string|max:2000',
        ]);

        if (!Auth::user()->ai_enabled) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI features are not enabled for your account.',
            ], 403);
        }

        $budget = Auth::user()->currentBudget;

        if (!$budget) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active budget.',
            ], 403);
        }

        $result = $this->voiceService->parse(
            $validated['transcript'],
            $budget
        );

        return response()->json($result);
    }
}
