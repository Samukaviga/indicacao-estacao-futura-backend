<?php

namespace App\DTOs;

use Illuminate\Support\Str;

class CreateRegistrationDTO
{
    public function __construct(
        public readonly string $student_name,
        public readonly string $responsible_name,
        public readonly string $indicated_student_name,
        public readonly string $indicated_responsible_name,
        public readonly string $indicated_mobile_phone,
        public readonly ?string $indicated_email,
        public readonly ?string $indicated_date_of_birth,
        public readonly ?string $indicated_education_level,
        public readonly string $lead_source,

        public readonly ?string $utm_source = null,
        public readonly ?string $utm_medium = null,
        public readonly ?string $utm_campaign = null,
        public readonly ?string $utm_term = null,
        public readonly ?string $utm_content = null,
        public readonly ?string $gclid = null,
        public readonly ?string $fbclid = null,
        public readonly ?string $msclkid = null,
        public readonly ?string $referrer = null,
        public readonly ?string $landing_page = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $required = [
            'student_name',
            'responsible_name',
            'indicated_student_name',
            'indicated_responsible_name',
            'indicated_mobile_phone',
        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $dob = self::toYmdDate((string) $data['indicated_date_of_birth']);


        return new self(
            student_name:        trim((string) $data['student_name']),
            responsible_name:    trim((string) $data['responsible_name']),
            indicated_student_name:        trim((string) $data['indicated_student_name']),
            indicated_responsible_name:    trim((string) $data['indicated_responsible_name']),
            indicated_mobile_phone:        self::cleanPhoneMask((string) $data['indicated_mobile_phone']),
            indicated_email:               strtolower(trim((string) $data['indicated_email'])) == '' ? null : strtolower(trim((string) $data['indicated_email'])),
            indicated_date_of_birth:       $dob == '' ? null : $dob,
            indicated_education_level:     trim((string) $data['indicated_education_level']),

            lead_source:         trim((string) $data['lead_source']),

            utm_source:   self::nullIfEmpty($data['utm_source']   ?? null),
            utm_medium:   self::nullIfEmpty($data['utm_medium']   ?? null),
            utm_campaign: self::nullIfEmpty($data['utm_campaign'] ?? null),
            utm_term:     self::nullIfEmpty($data['utm_term']     ?? null),
            utm_content:  self::nullIfEmpty($data['utm_content']  ?? null),
            gclid:        self::nullIfEmpty($data['gclid']        ?? null),
            fbclid:       self::nullIfEmpty($data['fbclid']       ?? null),
            msclkid:      self::nullIfEmpty($data['msclkid']      ?? null),
            referrer:     self::nullIfEmpty($data['referrer']     ?? null),
            landing_page: self::nullIfEmpty($data['landing_page'] ?? null),
        );
    }

    private static function nullIfEmpty(?string $v): ?string
    {
        if ($v === null) return null;
        $t = trim($v);
        return $t === '' ? null : $t;
    }

    /**
     * Converte "dd/mm/yyyy" em "Y-m-d". Se já vier "Y-m-d", mantém.
     */
    private static function toYmdDate(string $date): string
    {
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            [$d, $m, $y] = explode('/', $date);
            return sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
        }
        return $date;
    }

    /**
     * Remove máscara do telefone
     */
    private static function cleanPhoneMask(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone);
    }

  

    public function toArray(): array
    {
        $base = [
            'student_name'     => $this->student_name,
            'responsible_name' => $this->responsible_name,
            'indicated_student_name'     => $this->indicated_student_name,
            'indicated_responsible_name' => $this->indicated_responsible_name,
            'indicated_mobile_phone'     => $this->indicated_mobile_phone,
            'indicated_email'            => $this->indicated_email,
            'indicated_date_of_birth'    => $this->indicated_date_of_birth,
            'indicated_education_level'  => $this->indicated_education_level,
            
            'lead_source'      => $this->lead_source,

            'utm_source'   => $this->utm_source,
            'utm_medium'   => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term'     => $this->utm_term,
            'utm_content'  => $this->utm_content,
            'gclid'        => $this->gclid,
            'fbclid'       => $this->fbclid,
            'msclkid'      => $this->msclkid,
            'referrer'     => $this->referrer,
            'landing_page' => $this->landing_page,
        ];

        return array_filter($base, static fn($v) => !is_null($v));
    }
}