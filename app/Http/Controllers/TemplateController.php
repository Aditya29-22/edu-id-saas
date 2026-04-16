<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Services\ImageService;
use App\Services\S3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct(
        private S3Service $s3Service,
        private ImageService $imageService
    ) {}

    public function index(): JsonResponse
    {
        $user = auth()->user();

        $templates = Template::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('school_id')
                  ->orWhere('school_id', $user->school_id);
            })
            ->orderBy('type')
            ->get();

        return response()->json(['success' => true, 'data' => $templates]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'school_id' => 'nullable|exists:schools,id',
            'type' => 'required|in:system,custom',
            'front_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'back_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'layout' => 'nullable|json',
        ]);

        $schoolCode = $validated['school_id']
            ? \App\Models\School::find($validated['school_id'])->code
            : 'system';

        $frontResult = $this->s3Service->upload(
            $request->file('front_image'),
            $schoolCode,
            'templates',
            'front_' . time() . '.jpg'
        );

        $templateData = [
            'name' => $validated['name'],
            'school_id' => $validated['school_id'],
            'type' => $validated['type'],
            'front_image_url' => $frontResult['cdn_url'] ?: $frontResult['s3_url'],
            'front_image_s3_key' => $frontResult['s3_key'],
            'layout' => $validated['layout'] ? json_decode($validated['layout'], true) : null,
        ];

        if ($request->hasFile('back_image')) {
            $backResult = $this->s3Service->upload(
                $request->file('back_image'),
                $schoolCode,
                'templates',
                'back_' . time() . '.jpg'
            );
            $templateData['back_image_url'] = $backResult['cdn_url'] ?: $backResult['s3_url'];
            $templateData['back_image_s3_key'] = $backResult['s3_key'];
        }

        $template = Template::create($templateData);

        return response()->json([
            'success' => true,
            'message' => 'Template uploaded.',
            'data' => $template
        ], 201);
    }
}
