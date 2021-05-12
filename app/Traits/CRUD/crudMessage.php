<?php

namespace App\Traits\CRUD;

trait crudMessage
{
    /**
     * @param int $type
     * @param string $elementName
     * @param array|null $modifier
     * @return string
     */
    private function generateCrudMessage(int $type, string $elementName, array $modifier = null)
    {
        switch ($type) {
            case 1:
                $message = ucfirst($elementName) . " agregado existosamente";
                break;
            case 2:
                $message = ucfirst($elementName) . " actualizado existosamente";
                break;
            case 3:
                $message = ucfirst($elementName) . " eliminado existosamente";
                break;
            case 4:
                $message = "No se puede eliminar " . strtolower($elementName) . " ya que tiene una asociación con " . strtolower($modifier["constraint"]);
                break;
            default:
                $message = "OCURRIÓ UN ERROR AL PROCESAR TU SOLICITUD";
                break;
        }

        return $message;
    }
}
