<?php
namespace App;

use App\MysqlClient;

class LootGame
{
    private $db;

    public function __construct(){
        $mysql = new MysqlClient;
        $this->db = $mysql->db;
    }

    // TODO
    public function consultarInventario($user){

    }

    public function consultarPontos($user){
        $id = $this->checarUsuario($user);
        $result = $this->db->query('SELECT id,pontos FROM usuarios WHERE id = '.$id.' LIMIT 1'); 
        if($result->num_rows == 0){
            return " não foram encontrados pontos para o usuário";
        } else {
            $result = $result->fetch_object();
            return " seus pontos: ".$result->pontos;
        }
    }

    public function checarUsuario($user){
        $user = $this->db->real_escape_string($user);
        $result = $this->db->query('SELECT id FROM usuarios WHERE login = "'.$user.'" LIMIT 1');
        if($result->num_rows == 0){
            $this->db->query('INSERT INTO usuarios (login,pontos) VALUE ("'.$user.'",0)');
            $id = $this->db->insert_id;
        } else {
            $result = $result->fetch_assoc();
            $id = $result['id'];
        }
        
        return $id;
    }

    public function sortearItem(){
        $result = $this->db->query('SELECT * FROM itens ORDER BY RAND() LIMIT 1');
        return $result->fetch_object();
    }

    public function lootDiario($user_id){
        $hoje = date('Y-m-d');
        $result = $this->db->query('SELECT id FROM resgates 
                                    WHERE data_resgate = "'.$hoje.'"
                                    AND usuario_id = '.$user_id.' LIMIT 1');
        if($result->num_rows != 0){
            return true;
        } else {
            return false;
        }
    }

    public function atualizarPontuacao($user,$pontos){
        $result = $this->db->query('SELECT id,pontos FROM usuarios
                                    WHERE id = '.$user.'
                                    LIMIT 1');        
        $user = $result->fetch_object();
        $pontuacao = $user->pontos + $pontos;
        $this->db->query('UPDATE usuarios SET pontos = '.$pontuacao.' WHERE id = '.$user->id);        
    }

    public function coletaItem($user,$item,$pontos){
        $result = $this->db->query('SELECT qtde FROM inventarios 
                                    WHERE usuario_id = '.$user.'
                                    AND item_id = '.$item.'
                                    LIMIT 1');
        if($result->num_rows == 0){
            $this->db->query('INSERT INTO inventarios 
                                    (usuario_id,item_id,qtde) 
                                    VALUE ('.$user.','.$item.',1)');
        } else {
            $result = $result->fetch_assoc();
            $qtde = $result['qtde'] + 1;
            $this->db->query('UPDATE inventarios 
                                    SET qtde = '.$qtde.' 
                                    WHERE usuario_id = '.$user.' AND item_id = '.$item.')');            
        }
        // Atualizar pontuação
        $this->atualizarPontuacao($user,$pontos);
        // Gravar o loot do dia
        $this->db->query('INSERT INTO resgates
        (usuario_id,data_resgate) 
        VALUE ('.$user.',"'.date('Y-m-d').'")');
    }

    public function receberLoot($user){
        $id = intval($this->checarUsuario($user));
        $msg = "@$user ";
        // Verifica loot do dia
        if($this->lootDiario($id)){
            $msg .= "você já realizou sua tentativa de loot nessa live!";
            return $msg;
        }

        $item = $this->sortearItem();
        $coleta = rand(0,99);
        if($coleta <= $item->chance){
            // Sucesso
            $msg .= " você recebeu um #$item->nome (Pts:$item->valor)#";
            $this->coletaItem($id,$item->id,$item->valor);
        } else {
            // Falha
            $msg .= " você falhou ao obter um #$item->nome (Pts:$item->valor)#";
        }

        return $msg;
    }
}