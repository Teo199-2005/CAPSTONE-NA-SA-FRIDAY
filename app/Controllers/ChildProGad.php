<?php

declare(strict_types=1);

namespace App\Controllers;

class ChildProGad extends BaseController
{
    public function childpro(): string
    {
        return $this->renderPage('childpro');
    }

    public function gad(): string
    {
        return $this->renderPage('gad');
    }

    private function renderPage(string $tab): string
    {
        helper(['childpro_gad', 'asset', 'landing']);

        $hero = childpro_gad_hero_get($tab);
        $heroUrl = childpro_gad_media_url($hero['image']);
        $sections = childpro_gad_sections_get($tab);

        // Prepare sections with rendered media URLs
        $sectionData = [];
        foreach ($sections as $section) {
            $mediaUrl = $section['media_url'];
            $displayUrl = $mediaUrl;

            // Only convert local upload paths to full URLs if not already an external URL
            if ($mediaUrl !== '' && ! preg_match('#^https?://#', $mediaUrl)) {
                $displayUrl = childpro_gad_media_url($mediaUrl);
            }

            $sectionData[] = [
                'title'       => $section['title'],
                'description' => $section['description'],
                'media_type'  => $section['media_type'],
                'media_url'   => $mediaUrl,
                'display_url' => $displayUrl,
                'is_youtube'  => ($section['media_type'] === 'video' && childpro_gad_is_youtube_url($mediaUrl)),
                'embed_url'   => ($section['media_type'] === 'video' && childpro_gad_is_youtube_url($mediaUrl))
                    ? childpro_gad_youtube_embed_url($mediaUrl)
                    : '',
            ];
        }

        $tabLabel = childpro_gad_tab_label($tab);

        return view('childpro_gad', [
            'title'       => $tabLabel . ' — Cauayan South Central School',
            'tab'         => $tab,
            'tabLabel'    => $tabLabel,
            'hero'        => $hero,
            'heroUrl'     => $heroUrl,
            'sections'    => $sectionData,
            'heroSlides'  => landing_hero_slides_for_view(),
            'stripText'   => landing_announcement_strip_text(),
        ]);
    }
}