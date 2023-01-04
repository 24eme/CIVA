<?php

class ExportSVJson
{
    protected $sv;
    protected $raw;

    public function __construct(SV $declaration)
    {
        $this->sv = $declaration;
    }

    public function isValide()
    {
        return $this->sv->valide->statut === "VALIDE";
    }

    public function export()
    {
        $json = json_encode($this->raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_last_error_msg();
            return false;
        }

        return $json.PHP_EOL;
    }
}
