<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * Display the imprint page.
     */
    public function imprint(): View
    {
        $content = $this->formatContent(config('legals.imprint.content'));

        return view('legals.imprint', compact('content'));
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        $content = $this->formatContent(config('legals.privacy_policy.content'));

        return view('legals.privacy', compact('content'));
    }

    /**
     * Display the terms of service page.
     */
    public function terms(): View
    {
        $content = $this->formatContent(config('legals.terms_of_service.content'));

        return view('legals.terms', compact('content'));
    }

    /**
     * Format content with proper HTML structure and styling.
     */
    private function formatContent(string $content): string
    {
        $content = $this->replacePlaceholders($content);
        
        // Wrap h2 sections in styled cards
        $content = preg_replace_callback(
            '/<h2>(.*?)<\/h2>(.*?)(?=<h2>|$)/s',
            function ($matches) {
                $title = $matches[1];
                $body = trim($matches[2]);
                
                return '
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl overflow-hidden">
                    <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent"></div>
                    <div class="p-6 lg:p-8">
                        <h2 class="text-2xl font-bold text-(--color-accent-300) mb-6 flex items-center gap-3">
                            <div class="w-1 h-8 bg-(--color-accent-500) rounded-full"></div>
                            ' . $title . '
                        </h2>
                        <div class="space-y-4 text-zinc-300 leading-relaxed">
                            ' . $body . '
                        </div>
                    </div>
                </div>';
            },
            $content
        );
        
        // Style h3 headings
        $content = preg_replace(
            '/<h3>(.*?)<\/h3>/',
            '<h3 class="text-lg font-semibold text-zinc-200 mt-6 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-chevron-right text-(--color-accent-400) text-sm"></i>
                $1
            </h3>',
            $content
        );
        
        // Style paragraphs
        $content = preg_replace('/<p>/', '<p class="text-zinc-300 leading-relaxed">', $content);
        
        // Style lists
        $content = preg_replace('/<ul>/', '<ul class="space-y-2 ml-4">', $content);
        $content = preg_replace(
            '/<li>/',
            '<li class="text-zinc-300 flex items-start gap-3">
                <i class="fa-solid fa-circle text-(--color-accent-500) text-xs mt-2"></i>
                <span class="flex-1">',
            $content
        );
        $content = preg_replace('/<\/li>/', '</span></li>', $content);
        
        // Style links
        $content = preg_replace(
            '/<a href="(.*?)"(.*?)>/',
            '<a href="$1"$2 class="text-(--color-accent-400) hover:text-(--color-accent-300) underline decoration-1 underline-offset-2 transition-colors">',
            $content
        );
        
        // Style strong tags
        $content = preg_replace('/<strong>/', '<strong class="text-zinc-100 font-semibold">', $content);
        
        return $content;
    }

    /**
     * Replace placeholders in content with actual values.
     */
    private function replacePlaceholders(string $content): string
    {
        $replacements = [
            '{{company_name}}' => config('legals.company.name'),
            '{{company_address}}' => config('legals.company.address'),
            '{{company_city}}' => config('legals.company.city'),
            '{{company_country}}' => config('legals.company.country'),
            '{{company_email}}' => config('legals.company.email'),
            '{{company_phone}}' => config('legals.company.phone'),
            '{{company_website}}' => config('legals.company.website'),
            '{{responsible_name}}' => config('legals.responsible_person.name'),
            '{{responsible_title}}' => config('legals.responsible_person.title'),
            '{{responsible_email}}' => config('legals.responsible_person.email'),
            '{{dpo_name}}' => config('legals.data_protection_officer.name'),
            '{{dpo_email}}' => config('legals.data_protection_officer.email'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
