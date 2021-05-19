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
                $message = ucfirst($elementName) . " created successfully";
                break;
            case 2:
                $message = ucfirst($elementName) . " updated successfully";
                break;
            case 3:
                $message = ucfirst($elementName) . " deleted successfully";
                break;
            case 4:
                $message = "Unable to delete <strong>" . strtolower($elementName) . "</strong> because it has a relationship with <strong>" . strtolower($modifier["constraint"]) . "</strong>";
                break;
            default:
                $message = "There was an error processing your request";
                break;
        }

        return $message;
    }
}
