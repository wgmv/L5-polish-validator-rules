<?php
namespace KDuma\Validator;

use Illuminate\Contracts\Validation\Rule;


class PeselNumberRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! preg_match('/^[0-9]{11}$/', $value)) { //sprawdzamy czy ciąg ma 11 cyfr
            return false;
        }

        if(! $this->checkValidDate($value)) {
            return false;
        }

        $arrSteps = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3]; // tablica z odpowiednimi wagami
        $intSum = 0;
        for ($i = 0; $i < 10; $i++) {
            $intSum += $arrSteps[$i] * $value[$i]; //mnożymy każdy ze znaków przez wagć i sumujemy wszystko
        }
        $int = 10 - $intSum % 10; //obliczamy sumć kontrolną
        $intControlNr = ($int == 10) ? 0 : $int;
        if ($intControlNr == $value[10]) { //sprawdzamy czy taka sama suma kontrolna jest w ciągu
            return true;
        }

        return false;
    }

    public function message()
    {
        return trans('validation.pesel') != 'validation.pesel'
            ? trans('validation.pesel')
            : trans('kd-validator::validation.pesel');
    }

    private function checkValidDate($value)
    {
        $date = [substr($value,2,2), substr($value,4,2), substr($value,0,2)];

        $check1 = checkdate(intval($date[0]), intval($date[1]), intval("19".$date[2])) && intval("19".$date[2]) < date('Y');
        $check2 = checkdate(intval($date[0]) - 20, intval($date[1]), intval("20".$date[2])) && intval("20".$date[2]) < date('Y');

        return $check1 || $check2;
    }
}
