<?php

namespace App\Http\Services;

class AiService
{
    public function chat(string $message): array
    {
        return [
            'response' => "As your AI HR Assistant, I'm here to help you. Based on the query '".htmlspecialchars($message)."', here is what I found in our company handbook: Standard work hours are 9 AM to 6 PM, leaves must be submitted 2 days in advance, and claims must have a valid receipt.",
            'confidence' => 0.95,
        ];
    }

    public function scanReceipt(?string $filePath): array
    {
        return [
            'vendor' => 'Uber India Technologies Pvt Ltd',
            'amount' => 450.00,
            'currency' => 'INR',
            'date' => date('Y-m-d'),
            'category' => 'travel',
            'confidence_scores' => [
                'vendor' => 0.98,
                'amount' => 0.99,
                'date' => 0.95,
            ],
        ];
    }

    public function generateReview(string $employeeName, string $cycle, int $rating): array
    {
        $ratingTexts = [
            1 => 'Unsatisfactory',
            2 => 'Needs Improvement',
            3 => 'Meets Expectations',
            4 => 'Exceeds Expectations',
            5 => 'Outstanding',
        ];

        $ratingStr = $ratingTexts[$rating] ?? 'Meets Expectations';

        return [
            'review_markdown' => "### Performance Evaluation - {$cycle}\n\n**Employee Name:** {$employeeName}\n**Overall Rating:** {$rating} ({$ratingStr})\n\n#### Strengths\n- Consistently shows great commitment and collaborative teamwork.\n- Keeps clear lines of communication with managers and stakeholders.\n\n#### Areas of Improvement\n- Focus on timely execution of milestone deadlines.\n- Enhance technical domain expertise on cloud services.",
            'suggested_goals' => [
                'Complete AWS Associate certification within 6 months.',
                'Improve project delivery cycle times by 10%.',
            ],
        ];
    }

    public function parseResume(?string $filePath): array
    {
        return [
            'candidate_name' => 'Aditya Sharma',
            'email' => 'aditya.sharma@example.com',
            'phone' => '+91-9876543210',
            'skills' => ['Laravel', 'PHP', 'Vue.js', 'MySQL', 'Git', 'Docker'],
            'experience_years' => 4.5,
            'education' => 'B.Tech in Computer Science',
            'parsed_successfully' => true,
        ];
    }

    public function craftAnnouncement(string $title, string $priority, ?string $additionalInstructions): array
    {
        $priorityLabel = strtoupper($priority);

        return [
            'subject' => "Important: {$title}",
            'content' => "Dear Team,\n\nWe are writing to announce: {$title}.\n\nPriority Level: {$priorityLabel}\n\n".($additionalInstructions ? "Please note: {$additionalInstructions}\n\n" : '')."Best regards,\nHR Management Team",
        ];
    }
}
