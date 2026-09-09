<?php

declare(strict_types=1);

if (! function_exists('nationality_options')) {
    /**
     * @return list<string>
     */
    function nationality_options(): array
    {
        return [
            'Filipino',
            'American',
            'Australian',
            'British',
            'Canadian',
            'Chinese',
            'Indian',
            'Indonesian',
            'Japanese',
            'Korean',
            'Malaysian',
            'Singaporean',
            'Thai',
            'Vietnamese',
            'Other',
        ];
    }
}

if (! function_exists('emergency_contact_relationship_options')) {
    /**
     * @return list<string>
     */
    function emergency_contact_relationship_options(): array
    {
        return [
            'Father',
            'Mother',
            'Guardian',
            'Grandfather',
            'Grandmother',
            'Uncle',
            'Aunt',
            'Stepfather',
            'Stepmother',
            'Sibling',
            'Other',
        ];
    }
}

if (! function_exists('emergency_contact_relationship_rule')) {
    function emergency_contact_relationship_rule(): string
    {
        return 'required|in_list[' . implode(',', emergency_contact_relationship_options()) . ']';
    }
}
