<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
            'student_name'     => 'required|string|max:150',
            'responsible_name' => 'required|string|max:150',

            'indicated_student_name' => 'required|string|max:150',
            'indicated_responsible_name' => 'required|string|max:150',
            'indicated_mobile_phone'     => ['required','string','regex:/^\d{10,11}$/'],
            'indicated_email'            => 'nullable|email:rfc,dns|max:150',
            'indicated_date_of_birth'    => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'indicated_education_level'  => 'nullable|string|max:150',

            'lead_source'      => 'required|string|max:100',

            'utm_source'       => 'nullable|string|max:100',
            'utm_medium'       => 'nullable|string|max:100',
            'utm_campaign'     => 'nullable|string|max:150',
            'utm_term'         => 'nullable|string|max:150',
            'utm_content'      => 'nullable|string|max:150',
            'gclid'            => 'nullable|string|max:255',
            'fbclid'           => 'nullable|string|max:255',
            'msclkid'          => 'nullable|string|max:255',
            'referrer'         => 'nullable|string|max:255',
            'landing_page'     => 'nullable|string|max:255',
        ];
    }

     protected function prepareForValidation(): void
    {
        $digits = fn (?string $v) => $v !== null ? preg_replace('/\D+/', '', (string) $v) : null;
        $trimOrNull = fn ($v) => ($v === null) ? null : (trim((string) $v) === '' ? null : trim((string) $v));

        $dob = $this->indicated_date_of_birth ?? null;
        if (is_string($dob)) {
            $dob = trim($dob);
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dob)) {
                [$d, $m, $y] = explode('/', $dob);
                $dob = sprintf('%04d-%02d-%02d', (int) $y, (int) $m, (int) $d);
            }
        }

        $email = $this->indicated_email ? strtolower(trim((string) $this->indicated_email)) : null;

        $normalized = [
            'student_name'     => $trimOrNull($this->student_name),
            'responsible_name' => $trimOrNull($this->responsible_name),
            'indicated_student_name'     => $trimOrNull($this->indicated_student_name),
            'indicated_responsible_name' => $trimOrNull($this->indicated_responsible_name),
            'indicated_mobile_phone'     => $digits($this->indicated_mobile_phone),
            'indicated_email'            => $email,
            'indicated_date_of_birth'    => $dob,
            'indicated_education_level'  => $trimOrNull($this->indicated_education_level),
          

            'utm_source'       => $trimOrNull($this->utm_source),
            'utm_medium'       => $trimOrNull($this->utm_medium),
            'utm_campaign'     => $trimOrNull($this->utm_campaign),
            'utm_term'         => $trimOrNull($this->utm_term),
            'utm_content'      => $trimOrNull($this->utm_content),
            'gclid'            => $trimOrNull($this->gclid),
            'fbclid'           => $trimOrNull($this->fbclid),
            'msclkid'          => $trimOrNull($this->msclkid),
            'referrer'         => $trimOrNull($this->referrer),
            'landing_page'     => $trimOrNull($this->landing_page),
        ];

        $leadSource = $trimOrNull($this->lead_source);
        if ($leadSource === null) {
            $leadSource = $this->inferLeadSource($normalized);
        }
        $normalized['lead_source'] = $leadSource;

        $this->merge($normalized);
    }

    private function inferLeadSource(array $a): string
    {
        $src = strtolower((string) ($a['utm_source'] ?? ''));
        $med = strtolower((string) ($a['utm_medium'] ?? ''));
        $ref = strtolower((string) ($a['referrer'] ?? ''));

        if ($src === 'google' || !empty($a['gclid'])) return 'google_ads';
        if (str_contains($src, 'facebook') || !empty($a['fbclid'])) return 'facebook_ads';
        if (str_contains($src, 'instagram')) return 'instagram';
        if (str_contains($src, 'tiktok')) return 'tiktok';
        if (str_contains($src, 'linkedin')) return 'linkedin';
        if ($med === 'email' || str_contains($src, 'mail')) return 'email';
        if (in_array($med, ['cpc','ppc','paid'], true)) return 'paid_search';
        if ($src === 'direct' || ($src === '' && $ref === '')) return 'direct';
        if (str_contains($ref, 'google.')) return 'google_organic';
        if (str_contains($ref, 'bing.')) return 'bing_organic';
        if (str_contains($ref, 'facebook.')) return 'facebook_organic';
        if (str_contains($ref, 'instagram.')) return 'instagram_organic';
        return $src ?: 'unknown';
    }

}
