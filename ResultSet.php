<?php

/**
 * Description of ResultSet
 *
 * @author jnda
 */
class ResultSet {

    protected $result;

    public function __construct($r) {
        $this->result = $r;
    }

    public function fetchRow() {
        return mysql_fetch_array($this->result);
    }

    public function fetchAssoc() {
        return mysql_fetch_assoc($this->result);
    }

    function drawHTMLTable() {
        echo "<table border ='1'>\n";
        $numFiels = mysql_num_fields($this->result);
        echo "<tr>";
        for ($i = 0; $i < $numFiels; $i++) {
            echo "<th>" . mysql_field_name($this->result, $i) . "</th>";
        }
        echo "</tr>\n";
        while (($row = $this->fetchRow()) != null) {
            echo "<tr>";
            for ($i = 0; $i < $numFiels; $i++) {
                echo "<td>$row[$i]</td>";
            }
            echo"</td>\n";
        }
        echo "</table>\n";
    }

    /**
     * Första fältet ska vara nyckeln och visas inte
     * Andra fältet blir länken
     * Sedan kommer ett valbart stilfält
     * Sedan kommer flera valbara katerifält
     * Därefter kommer alla vanliga fält
     *
     * @param String $baseUrl Hela URLen fast utan det sista variabelvärdet
     * @param int $kategorier Anger hur många fält som det ska grupperas efter
     * @param int $groupconcat Anger att man vill slå ihop alla värden i denna kolumn som har samma id (kolumn 0)
     * @param int $slaIhop Anger att man vill slå ihop denna kolumn med nästa
     */
    function drawHTMLTableWithLink($baseUrl, $kategorier = 0, $groupconcat=0, $slaIhop=0, $anvandStil=false) {
        echo "<table border ='1'>\n";
        $numFiels = mysql_num_fields($this->result);
        echo "<tr>";
        $stilIndex = 1;

        $forstaVanligaIndex = 2 + $kategorier;
        $linkIndex = 1 + $kategorier;
        $forstaKategori = 1;
        if ($anvandStil) {
            $forstaVanligaIndex++;
            $linkIndex++;
            $forstaKategori++;
        }

        //echo '$forstaVanligaIndex: '.$forstaVanligaIndex;

        $colspan = $numFiels - $linkIndex;

        for ($i = $linkIndex; $i < $numFiels; $i++) {
            if ($i != ($slaIhop+1)) {
                echo "<th>" . mysql_field_name($this->result, $i) . "</th>";
            }
        }
        echo "</tr>\n";
        for ($i = $forstaKategori; $i < $kategorier + $forstaKategori; $i++) {
            $kategori[$i] = null;
        }
        for ($i = $forstaKategori; $i < $kategorier + $forstaKategori; $i++) {
            $kategoriField[$i] = mysql_field_name($this->result, $i);
        }
        $row = $this->fetchRow();
        while ($row != null) {

            $kategoriForeAndrad = false;
            for ($i = $forstaKategori; $i < $kategorier + $forstaKategori; $i++) {
                if ($kategori[$i] != $row[$i] || $kategoriForeAndrad) {
                    $kategori[$i] = $row[$i];
                    $kategoriForeAndrad = true;
//                    $stilstart = "<h".($i+2).">";
//                    $stilslut = "</h".($i+2).">";
//                    $stilstart = "<b>";
//                    $stilslut = "</b>";
                    $stil = "class='gruppering" . ($i - $forstaKategori + 1) . "'";
//                    echo "<tr><td colspan=$colspan>".$stilstart.$kategoriField[$i]."   ".$kategori[$i].$stilslut."</td></tr>\n";
                    echo "<tr><td colspan=$colspan $stil>" . $kategoriField[$i] . "   " . $kategori[$i] . "</td></tr>\n";
                }
            }
            if ($anvandStil) {
                $stil = "style='" . $row[$stilIndex] . "'";
            } else {
                $stil = "";
            }
            echo "<tr $stil>";
            $key = $row[0];
            echo "<td><a href=" . $baseUrl . $key . ">$row[$linkIndex]</a></td>";

            $newRow = $this->fetchRow();

            for ($i = $forstaVanligaIndex; $i < $numFiels; $i++) {
                $field = $row[$i];
                if (substr($field, 0, 3) != "<p>") {
                    $field = nl2br($field);
                }
//                $field = $row[$i];
                if (($i - 1) != $slaIhop) {
                    echo "<td>";
                }
                echo $field;
                if ($i == $groupconcat) {
                    //echo '$newRow[0]: '.$newRow[0] . ' $row[0]: '.$row[0].'<br>';
                    while ($newRow[0] == $row[0]) {
                        $field = nl2br($newRow[$i]);
                        echo '<br/>' . $field;
                        $newRow = $this->fetchRow();
                        //echo '$newRow[0]: '.$newRow[0] . ' $row[0]: '.$row[0].'<br>';

                    }
                }
                if ($i != $slaIhop) {
                    echo "</td>";
                } else {
                    echo "<br/>";
                }
            }
            echo"</td>\n";
            $row = $newRow;
        }
        echo "</table>\n";
    }

}

?>
