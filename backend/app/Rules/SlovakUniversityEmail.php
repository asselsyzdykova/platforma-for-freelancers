<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SlovakUniversityEmail implements ValidationRule
{
    protected array $allowedDomains = [
        '@uniba.sk',  //1Univerzita Komenského v Bratislave
            '@student.uniba.sk',
            '@stuba.sk', //2Slovenská technická univerzita v Bratislave
            '@student.stuba.sk',
            '@tuke.sk', //3Technická univerzita v Košiciach
            '@student.tuke.sk',
            '@tuzvo.sk', //4Technická univerzita vo Zvolene
            '@student.tuzvo.sk',
            '@ukf.sk', //5Univerzita Konštantína Filozofa v Nitre
            '@student.ukf.sk',
            '@umb.sk', //6Univerzita Mateja Bela v Banskej Bystrici
            '@student.umb.sk',
            '@upjs.sk', //7Univerzita Pavla Jozefa Šafárika v Košiciach
            '@student.upjs.sk',
            '@uniag.sk', //8Slovenská poľnohospodárska univerzita v Nitre
            '@student.uniag.sk',
            '@euba.sk', //9Ekonomická univerzita v Bratislave
            '@student.euba.sk',
            '@unipo.sk', //10Prešovská univerzita v Prešove
            '@student.unipo.sk',
            '@uniza.sk', //11Žilinská univerzita v Žiline
            '@student.uniza.sk',
            '@uvm.sk', //12Univerzita veterinárskeho lekárstva a farmácie v Košiciach
            '@student.uvm.sk',
            '@truni.sk', //13Trnavská univerzita v Trnave
            '@student.truni.sk',
            '@ucm.sk', //14Univerzita sv. Cyrila a Metoda v Trnave
            '@student.ucm.sk',
            '@szu.sk', //15Slovenská zdravotnícka univerzita v Bratislave
            '@student.szu.sk',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower($value);
        $isAllowed = false;

        foreach ($this->allowedDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            $fail('Freelancer email must belong to a Slovak university domain.');
        }
    }
}
