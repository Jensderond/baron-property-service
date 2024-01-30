<?php

declare(strict_types=1);

namespace App\Helpers;

class KeyTranslationsHelper
{
    public static function mainFunction(string $function): string
    {
        return match ($function) {
            "WINKELRUIMTE" => $function = "Winkelruimte",
            "KANTOORRUIMTE" => $function = "Kantoorruimte",
            "BEDRIJFSRUIMTE" => $function = "Bedrijfsruimte",
            "HORECA" => $function = "Horeca",
            "BOUWGROND" => $function = "Bouwgrond",
            "OVERIGE" => $function = "Overige",
            "LEISURE" => $function = "Leisure",
            "MAATSCHAPPELIJK_VASTGOED" => $function = "Maatschappelijk vastgoed",
            "VERHARD_BUITENTERREIN" => $function = "Verhard buitenterrein",
            "BELEGGING" => $function = "Belegging",
            default => $function = "Onbekend",
        };
    }

    public static function distance(string $distance): string
    {
        return match ($distance) {
            "OP_5000_M_OF_MEER" => $distance = "5000 m of meer",
            "OP_4000_M_TOT_5000_M" => $distance = "4000 m tot 5000 m",
            "OP_3000_M_TOT_4000_M" => $distance = "3000 m tot 4000 m",
            "OP_2000_M_TOT_3000_M" => $distance = "2000 m tot 3000 m",
            "OP_1500_M_TOT_2000_M" => $distance = "1500 m tot 2000 m",
            "OP_1000_M_TOT_1500_M" => $distance = "1000 m tot 1500 m",
            "OP_500_M_TOT_1000_M" => $distance = "500 m tot 1000 m",
            "OP_MINDER_DAN_500_M" => $distance = "Minder dan 500 m",
            default => $distance = "Onbekend",
        };
    }

    public static function facilities(array $facilities): string
    {
        $facilities = array_map(function ($facility) {
            return match ($facility) {
                "BETONVLOER" => $facility = "Betonvloer",
                "BRANDMELDINSTALLATIE_MET_DOORSCHAKELING_AANWEZIG" => $facility = "Brandmeldinstallatie met doorschakeling aanwezig",
                "BUITENRUIMTE_AANWEZIG" => $facility = "Buitenruimte aanwezig",
                "BUITENSCHOOLSE_KINDEROPVANG" => $facility = "Buitenschoolse kinderopvang",
                "EIGEN_PARKEERVOORZIENINGEN_AANWEZIG" => $facility = "Eigen parkeervoorzieningen aanwezig",
                "ELECTRA" => $facility = "Electra",
                "HEATER" => $facility = "Heater",
                "HORECA_AANWEZIG" => $facility = "Horeca aanwezig",
                "INBOUWARMATUREN" => $facility = "Inbouwarmaturen",
                "KABELGOTEN" => $facility = "Kabelgoten",
                "KAMERINDELING" => $facility = "Kamerindeling",
                "KAMPWINKEL" => $facility = "Kampwinkel",
                "KINDERDAGVERBLIJF" => $facility = "Kinderdagverblijf",
                "KRACHTSTROOM" => $facility = "Krachtstroom",
                "LICHTSTRATEN" => $facility = "Lichtstraten",
                "LIFT" => $facility = "Lift",
                "LIFTEN" => $facility = "Liften",
                "LOADING_DOCKS" => $facility = "Loading docks",
                "OVERHEADDEUREN" => $facility = "Overheaddeuren",
                "PANTRY" => $facility = "Pantry",
                "RECEPTIE" => $facility = "Receptie",
                "RIOLERING" => $facility = "Riolering",
                "SPRINKLER" => $facility = "Sprinkler",
                "SYSTEEMPLAFOND" => $facility = "Systeemplafond",
                "TE_OPENEN_RAMEN" => $facility = "Te openen ramen",
                "TOILET" => $facility = "Toilet",
                "TOILETGEBOUWEN" => $facility = "Toiletgebouwen",
                "VERWARMING" => $facility = "Verwarming",
                "ZWEMBAD" => $facility = "Zwembad",

                default => $facility = "Onbekend",
            };
        }, $facilities);

        return implode(", ", $facilities);
    }

    public static function energyClass(string $energyClass): string
    {
        return match ($energyClass) {
            "A" => $energyClass = "A",
            "A_P" => $energyClass = "A+",
            "A_PP" => $energyClass = "A++",
            "A_PPP" => $energyClass = "A+++",
            "A_PPPP" => $energyClass = "A++++",
            "A_PPPPP" => $energyClass = "A+++++",
            "B" => $energyClass = "B",
            "C" => $energyClass = "C",
            "D" => $energyClass = "D",
            "E" => $energyClass = "E",
            "F" => $energyClass = "F",
            "G" => $energyClass = "G",
            default => $energyClass = "Onbekend",
        };
    }

    public static function projectCategory(string $category): string
    {
        return match ($category) {
            "KOOP" => $category = "Koop",
            "HUUR" => $category = "Huur",
            "KOOP_EN_HUUR" => $category = "Koop en huur",
            default => $category = "Onbekend",
        };
    }

    public static function status(string $status): string
    {
        return match ($status) {
            "BESCHIKBAAR" => $status = "Beschikbaar",
            "GEANNULEERD" => $status = "Geannuleerd",
            "GEVEILD" => $status = "Geveild",
            "INGETROKKEN" => $status = "Ingetrokken",
            "IN_AANMELDING" => $status = "In aanmelding",
            "ONDER_BOD" => $status = "Onder bod",
            "ONDER_OPTIE" => $status = "Onder optie",
            "PROSPECT" => $status = "Prospect",
            "VERHUURD" => $status = "Verhuurd",
            "VERHUURD_ONDER_VOORBEHOUD" => $status = "Verhuurd onder voorbehoud",
            "VERKOCHT" => $status = "Verkocht",
            "VERKOCHT_BIJ_INSCHRIJVING" => $status = "Verkocht bij inschrijving",
            "VERKOCHT_ONDER_VOORBEHOUD" => $status = "Verkocht onder voorbehoud",
            default => $status = "Onbekend",
        };
    }
}
