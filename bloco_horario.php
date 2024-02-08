<?php

require_once (__DIR__) . ("\connector.php");
class bloco_horario
{
    public ?int $id = null;
    public ?String $horas = null;
    public ?String $hora_inicial = null;
    public ?String $hora_final = null;
    public ?String $turma = null;
    public ?String $ano = null;
    public ?String $disciplina = null;
    public ?String $docente = null;
    public ?String $sala = null;
    public ?String $dia_da_semana = null;

    function __construct(int $id = null, String $horas = null,String $hora_inicial = null, String $hora_final = null, String $turma = null, String $ano = null, String $disciplina = null, String $docente = null, String $sala = null, String $dia_da_semana = null)
    {
        $this->id = $id;
        $this->horas = $horas;
        $this->hora_inicial = $hora_inicial;
        $this->hora_final = $hora_final;
        $this->turma = $turma;
        $this->ano = $ano;
        $this->disciplina = $disciplina;
        $this->docente = $docente;
        $this->sala = $sala;
        $this->dia_da_semana = $dia_da_semana;
    }

    public function save()
    {
        $this->id = $this->generateID();
        $comando = "INSERT INTO horario VALUES ($this->id, '$this->horas', '$this->hora_inicial', '$this->hora_final','$this->turma', '$this->ano', '$this->disciplina', '$this->docente', '$this->sala', '$this->dia_da_semana')";
        connector::connect()->query($comando);
    }

    private function generateID(): ?int
    {
        $command = "SELECT MAX(id)+1 maximum FROM `horario` WHERE 1";
        $valorMaximo = connector::connect()->query($command);

        if ($valorMaximo->num_rows > 0) {
            // output data of each row
            $row = $valorMaximo->fetch_assoc();
            if ($row["maximum"] == null) {
                return 1;
            } else {
                return intval($row["maximum"]);
            }
        }
        return null;
    }
}
