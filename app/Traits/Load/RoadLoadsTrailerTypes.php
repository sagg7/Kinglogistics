<?php

namespace App\Traits\Load;

trait RoadLoadsTrailerTypes
{
    private function getRoadLoadsTrailerTypes()
    {
        return [
            "Dry Van",
            "Flatbed",
            "Reefer",
            "Step Deck/Single Drop",
            "Double Drop",
            "Removable Gooseneck",
            "Maxi Flat",
            "Van+Vented",
            "Van+Airride",
            "Van+Intermodal",
            "Flat+Intermodal",
            "Reefer+Intermodal",
            "Curtain Van",
            "Container",
            "Hotshot",
            "Cargo Van",
            "Dump Trailer",
            "Auto Carrier",
            "Hopper Bottom",
            "Tanker",
            "Lowboy",
            "Landall",
            "Flat+Sides",
            "Flat+Tarp",
            "Power Only",
            "Other",
        ];
    }

    private function getRoadLoadsAbbreviatedTrailerTypes()
    {
        return [
            "V",
            "F",
            "R",
            "SD",
            "DD",
            "RGN",
            "MX",
            "VV",
            "VA",
            "VINT",
            "FINT",
            "RINT",
            "CV",
            "CONT",
            "HS",
            "CRG",
            "DT",
            "AC",
            "HB",
            "TNK",
            "LB",
            "LA",
            "FS",
            "FT",
            "PO",
            "O",
        ];
    }
}
