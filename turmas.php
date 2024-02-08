<?php

include("./bloco_horario.php");
$directory = './horarios-ispgaya/';
$folder = scandir($directory);

echo ("<b>Dados Inseridos na Base de Dados com Sucesso!</b>");

foreach ($folder as $file) {

    $dia = 1;

    if (is_dir($directory . $file))
        continue;

    $conteudo = file_get_contents($directory . $file);

    $doc = new DOMDocument();
    @$doc->loadHTML($conteudo);
    $xpath = new DOMXpath($doc);

    $disciplina = 'td_evento_1';
    $res_disciplina = $xpath->query("//td[contains(@class, '$disciplina')]");
    $res_horas = $xpath->query('//td[@class="td_lateral"]');
    $tdDisciplina = $xpath->query("//td[contains(@class, '$disciplina')]");

    for ($a = 0; $a < $res_disciplina->length; $a++) {

        if ($res_disciplina->length > 00) {
            //NOME DA TURMA (informacao referente a horarios das turmas)
            $res_nometurma = $xpath->query('//title');
            if ($res_nometurma->length > 0) {
                $nome_turma = str_replace("Horário da turma ", "", $res_nometurma->item(0)->nodeValue);
            }

            //ANO DA TURMA (informacao referente a horarios das turmas)
            $classeturma = 'cabtitulo';
            $res_anoturma = $xpath->query("//*[contains(@class, '$classeturma')]");
            if ($res_anoturma->length > 0) {
                $cursos = array(
                    "Licenciatura em Contabilidade - Ano: ", "CTeSP em Electrónica e Automação Industrial - Ano: ", "Licenciatura em Engenharia Electrónica e Automação - Ano: ",
                    "Licenciatura em Engenharia Informática - Ano: ", "Licenciatura em Engenharia Mecânica - Ano: ", "Licenciatura em Gestão - Ano: ", "CTeSP em Gestão de PME - Ano: ",
                    "CTeSP em Markting Digital - Ano: ", "Mestrado em Cibersegurança e Auditoria de Sistemas Informáticos - Ano:", "CTeSP em Redes e Sistemas Informáticos - Ano: ",
                    "CTeSP em Tecnologia Mecatrónica - Ano: ", "CTeSP em Tecnologia e Programação de Sistemas Informáticos - Ano: ", "Licenciatura em Turismo - Ano: "
                );
                $ano_turma = str_replace($cursos, "", $res_anoturma->item(0)->nodeValue);
            }

            //NOME DO DOCENTE (informacao referente a horarios docentes)
            $classedocente = 'a1';
            $res_nomedocente = $xpath->query("//*[contains(@class, '$classedocente')]");
            if ($res_nomedocente->length > $a) {
                $nome_docente = ($res_nomedocente->item($a)->nodeValue);
            }

            //UNIDADE CURRICULAR (informacao referente a horarios docentes e das turmas)
            $reg_disc = '/\[.*\]/';
            $res_unidade = preg_replace($reg_disc, "", $res_disciplina->item($a)->nodeValue);

            //SALA (informacao referente a horarios docentes)
            $reg_sala = '/\((?:[^)(]*)*+\)|\{(?:[^}{]*+)*\}|\[(?:[^][]*+)*\](?:\[[^][]*]|\([^()]*\)|{[^{}]*})|[^][(){}]+\[(.*?)\]/';
            $res_sala = preg_replace($reg_sala, "", $res_disciplina->item($a)->nodeValue);

            //DIA (informacao referente a horarios docentes e das turmas)
            //Segunda - 3, terca - 5, quarta - 7, quinta - 9 , sexta 11, sabado 13
            $dia_da_Semana = array("Segunda", "Terca", "Quarta", "Quinta", "Sexta", "Sabado");
            for ($r = 0; $r < $tdDisciplina->item($a)->parentNode->childNodes->length; $r++) {
                if ($tdDisciplina->item($a)->parentNode->childNodes->item($r)->nodeValue == $tdDisciplina->item($a)->nodeValue && $tdDisciplina->item($a)->parentNode->childNodes->item($r)->getNodePath() == $tdDisciplina->item($a)->getNodePath()) {
                    if ($r == 3) {
                        $resultado_dia = $dia_da_Semana[0];
                    }
                    if ($r == 5) {
                        $resultado_dia = $dia_da_Semana[1];
                    }
                    if ($r == 7) {
                        $resultado_dia = $dia_da_Semana[2];
                    }
                    if ($r == 9) {
                        $resultado_dia = $dia_da_Semana[3];
                    }
                    if ($r == 11) {
                        $resultado_dia = $dia_da_Semana[4];
                    }
                    if ($r == 13) {
                        $resultado_dia = $dia_da_Semana[5];
                    }
                }
            }

            //HORAS (informacao referente a horarios docentes e das turmas)
            $res_horas = $xpath->query('//td[@class="td_lateral"]');
            $reg_horas = '/(.*)+09:00 - 09:30|09:30 - 10:00|10:00 - 10:30|10:30 - 11:00|11:00 - 11:30|11:30 - 12:00|12:00 - 12:30|12:30 - 13:00|13:00 - 13:30|13:30 - 14:00|14:00 - 14:30/m';
            $resultado_horas = preg_replace($reg_horas, "", $res_horas->item(0)->nodeValue);

            $resultado_horas = $tdDisciplina->item($a)->attributes->item(2)->nodeValue;
        }

        $hora_inicial = date("H:i", strtotime((explode(" - ", $tdDisciplina->item($a)->parentNode->nodeValue)[0])));

        $hora_final = date("H:i", strtotime((explode(" - ", explode("\n", $tdDisciplina->item($a)->parentNode->nodeValue)[1])[1])));

        $time = new DateTime($hora_final);
        $time->add(new DateInterval('PT' . ((intval($resultado_horas) - 1) * 30) . 'M'));
        $hora_final = $time->format('H:i');

        $id = 1;

        $horario = new bloco_horario(id: $id, horas: $resultado_horas, hora_inicial: $hora_inicial, hora_final: $hora_final, turma: $nome_turma, ano: $ano_turma, disciplina: $res_unidade, docente: $nome_docente, sala: $res_sala, dia_da_semana: $resultado_dia);
        $horario->save();
    }
}
