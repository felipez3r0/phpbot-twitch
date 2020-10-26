<?php
namespace App;

class GameD100
{
    private $rankingD100 = [];
    public $ativo = false;

    public function resetRanking(){
        $this->rankingD100 = [];
    }

    public function adicionarD100($user, $roll)
    {
        $msg = "";
        if (!isset($this->rankingD100[$user])) {
            $msg = '@' . $user . ' o resultado do seu d100 é ' . $roll;
            $this->rankingD100[$user] = $roll;
        } else {
            $msg = '@' . $user . ' a sua rolagem já foi realizada.';
        }

        return $msg;
    }

    public function finalizarJogo()
    {
        arsort($this->rankingD100);
        $top3 = 1;
        $msg = "CLASSIFICAÇÃO: ";
        foreach ($this->rankingD100 as $user => $roll) {
            $msg .= '@' . $user . ' ficou em ' . $top3 . 'º lugar com a rolagem - ' . $roll." | ";
            $top3++;
            if ($top3 > 3) {
                break;
            }
        }

        $msg = substr($msg,0,-3);
        return $msg;        
    }
}