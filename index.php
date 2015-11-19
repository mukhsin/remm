<?php

include('inc/koneksi.php');

$appName = "REMM";

function getDosenList() {
    $dosenList = array();

    $dosen = "SELECT id_dosen, nama_dosen FROM dosen";
    $getDosen = mysql_query($dosen);
    $i = 0;

    while ($hasil = mysql_fetch_array($getDosen)) {
        $dosenList[$i] = array(
            'id_dosen' => $hasil['id_dosen'],
            'nama_dosen' => $hasil['nama_dosen'],
        );

        $i++;
    }

    return $dosenList;
}

function getMatkulList() {
    $matkulList = array();

    $matkul = "SELECT id_matkul, nama_matkul, sks FROM matkul";
    $getMatkul = mysql_query($matkul);
    $i = 0;

    while ($hasil = mysql_fetch_array($getMatkul)) {
        $matkulList[$i] = array(
            'id_matkul' => $hasil['id_matkul'],
            'nama_matkul' => $hasil['nama_matkul'],
            'sks' => $hasil['sks'],
        );

        $i++;
    }

    return $matkulList;
}

function getDosenMatkulRelationList() {
    $dosenMatkulRelationList = array();

    $dosenMatkulRelation = "SELECT id_dosen, id_matkul FROM and_dosen_matkul";
    $getDosenMatkulRelation = mysql_query($dosenMatkulRelation);
    $i = 0;

    while ($hasil = mysql_fetch_array($getDosenMatkulRelation)) {
        $dosenMatkulRelationList[$i] = array(
            'id_dosen' => $hasil['id_dosen'],
            'id_matkul' => $hasil['id_matkul'],
        );

        $i++;
    }

    return $dosenMatkulRelationList;
}

function getRuanganList() {
    $ruanganList = array();

    $ruangan = "SELECT id_ruangan, nama_ruangan FROM ruangan";
    $getRuangan = mysql_query($ruangan);
    $i = 0;

    while ($hasil = mysql_fetch_array($getRuangan)) {
        $ruanganList[$i] = array(
            'id_ruangan' => $hasil['id_ruangan'],
            'nama_ruangan' => $hasil['nama_ruangan'],
        );

        $i++;
    }

    return $ruanganList;
}

function indexToTimeslot($index) {
    $h = $index / 11 + 1;
    $w = $index % 11 + 100 + 1;
    $timeslot = 'H' . floor($h) . 'W' . substr($w, -2);
    return $timeslot;
}

function timeslotToIndex($timeslot) {
    $h = intval(substr($timeslot, 1, 1));
    $w = intval(substr($timeslot, -2));
    $index = ($h - 1) * 11 + ($w - 1);
    return $index;
}

function getHariFrom($timeslot) {
    $numberHari = substr($timeslot, 1, 1);
    switch ($numberHari) {
        case '1':
            return "Senin";
        case '2':
            return "Selasa";
        case '3':
            return "Rabu";
        case '4':
            return "Kamis";
        case '5':
            return "Jumat";
        case '6':
            return "Sabtu";
        case '7':
            return "Minggu";
    }
}

function getDurasiFrom($timeslot) {
    $i = timeslotToIndex($timeslot);
    $j_mulai = (($i % 11) * 50 + 20) / 60 + 7 + 100;
    $m_mulai = (($i % 11) * 50 + 20) % 60 + 100;
    $j_selesai = (($i % 11 + 1) * 50 + 20) / 60 + 7 + 100;
    $m_selesai = (($i % 11 + 1) * 50 + 20) % 60 + 100;

    $durasi_timeslot = substr(floor($j_mulai), -2) . '.' .
                substr($m_mulai, -2) . ' - ' .
                substr(floor($j_selesai), -2) . '.' .
                substr($m_selesai, -2);

    return $durasi_timeslot;
}

function getTimeslotList() {
    $timeslotList = array();

    for ($i = 0; $i < 5 * 11; $i++) {
        $id_timeslot = indexToTimeslot($i);
        $durasi_timeslot = getDurasiFrom($id_timeslot);
        $hari_timeslot = getHariFrom($id_timeslot);

        $timeslotList[$i] = array(
            'id_timeslot' => $id_timeslot,
            'hari_timeslot' => $hari_timeslot,
            'durasi_timeslot' => $durasi_timeslot,
        );
    }

    return $timeslotList;
}

function getRuanganTimeslotRelationList() {
    $ruanganList = getRuanganList();
    $timeslotList = getTimeslotList();
    $ruanganTimeslotRelationList = array();
    $relationId = 0;

    for ($i = 0; $i < count($ruanganList); $i++) {
        for ($j = 0; $j < count($timeslotList); $j++) {
            $ruanganTimeslotRelationList[$relationId] = array(
                'id_ruangan' => $ruanganList[$i]['id_ruangan'],
                'id_timeslot' => $timeslotList[$j]['id_timeslot'],
                'sedang_dipakai' => "t",
            );

            $relationId++;
        }
    }

    return $ruanganTimeslotRelationList;
}

function getDosenTimeslotRelationList() {
    $dosenList = getDosenList();
    $timeslotList = getTimeslotList();
    $dosenTimeslotRelationList = array();
    $relationId = 0;

    for ($i = 0; $i < count($dosenList); $i++) {
        for ($j = 0; $j < count($timeslotList); $j++) {
            $dosenTimeslotRelationList[$relationId] = array(
                'id_dosen' => $dosenList[$i]['id_dosen'],
                'id_timeslot' => $timeslotList[$j]['id_timeslot'],
                'sedang_mengajar' => "t",
            );

            $relationId++;
        }
    }

    return $dosenTimeslotRelationList;
}

function getSpotList() {
    $spotList = array();
    $matkulList = getMatkulList();
    $ruanganList = getRuanganList();
    $timeslotList = getTimeslotList();
    $index = 0;

    for ($i = 0; $i < count($matkulList); $i++) {
        for ($j = 0; $j < count($ruanganList); $j++) {
            for ($k = 0; $k < count($timeslotList); $k++) {
                $id_int = $index + 100000000 + 1;
                $id_spot = "SP" . substr($id_int, -8);

                $spotList[$index] = array(
                    'id_spot' => $id_spot,
                    'id_matkul' => $matkulList[$i]['id_matkul'],
                    'id_ruangan' => $ruanganList[$j]['id_ruangan'],
                    'id_timeslot' => $timeslotList[$k]['id_timeslot'],
                    'pheromone' => 0,
                );

                $index++;
            }
        }
    }

    return $spotList;
}

function getSpot($spotList, $id_matkul) {
    $index = rand(0, count($spotList) - 1);
    $id_spot = $spotList[$index]['id_spot'];

    if ($spotList[$index]['id_matkul'] == $id_matkul) {
        return $id_spot;
    } else {
        return getSpot($spotList, $id_matkul);
    }
}

function getProperSpot($spotList, $id_matkul, $sks, $ruanganTimeslotRelationList, $dosenTimeslotRelationList) {
    $id_spot = getSpot($spotList, $id_matkul);
    $timeslotList = array();
    $idSpotList = array();
    $id_dosen = "";
    $id_ruangan = "";
    $id_timeslot = "";
    // $id_spot_index = 0;

    for ($j = 0; $j < count($spotList); $j++) {
        if ($spotList[$j]['id_spot'] == $id_spot) {
            $id_spot_number = $j;
            $id_ruangan = $spotList[$j]['id_ruangan'];
            $id_timeslot = $spotList[$j]['id_timeslot'];
            $timeslotIndex = timeslotToIndex($id_timeslot);

            $dosenMatkulRelationList = getDosenMatkulRelationList();
            for ($k = 0; $k < count($dosenMatkulRelationList); $k++) {
                if ($dosenMatkulRelationList[$k]['id_matkul'] == $id_matkul) {
                    $id_dosen = $dosenMatkulRelationList[$k]['id_dosen'];
                }
            }

            for ($k = 0; $k < $sks; $k++) {
                // $currentTimeslot = indexToTimeslot($timeslotIndex);
                $currentTimeslot = indexToTimeslot($timeslotIndex + $k);

                for ($l = 0; $l < count($ruanganTimeslotRelationList); $l++) {
                    if ($ruanganTimeslotRelationList[$l]['id_ruangan'] == $id_ruangan &&
                        $ruanganTimeslotRelationList[$l]['id_timeslot'] == $currentTimeslot) {
                        if ($ruanganTimeslotRelationList[$l]['sedang_dipakai'] == "t") {
                            if ($k > 0) {
                                // pengecekan agar jadwal kuliah tetap di hari yang sama
                                $currentTimeslotIndex = indexToTimeslot($timeslotIndex + $k);
                                $prevTimeslotIndex = indexToTimeslot($timeslotIndex + $k - 1);

                                if (intval(substr($currentTimeslotIndex, 1, 1)) !=
                                    intval(substr($prevTimeslotIndex, 1, 1))) {
                                    unset($timeslotList);

                                    for ($m = 0; $m <= $k; $m++) {
                                        if ($ruanganTimeslotRelationList[$l]['id_ruangan'] == $id_ruangan &&
                                            $ruanganTimeslotRelationList[$l]['id_timeslot'] == $currentTimeslot) {
                                            $ruanganTimeslotRelationList[$l - $m]['sedang_dipakai'] = "t";
                                        }
                                    }

                                    return getProperSpot(
                                        $spotList,
                                        $id_matkul,
                                        $sks,
                                        $ruanganTimeslotRelationList,
                                        $dosenTimeslotRelationList
                                    );
                                }
                            }

                            for ($n = 0; $n < count($dosenTimeslotRelationList); $n++) {
                                if ($dosenTimeslotRelationList[$n]['id_dosen'] == $id_dosen &&
                                    $dosenTimeslotRelationList[$n]['id_timeslot'] == $currentTimeslot) {
                                    if ($dosenTimeslotRelationList[$n]['sedang_mengajar'] == "t") {
                                        $timeslotList[$k] = array(
                                            'id_timeslot' => $currentTimeslot,
                                            'durasi_timeslot' => getDurasiFrom($currentTimeslot),
                                        );

                                        $spotList[$j + $k]['pheromone'] = $spotList[$j + $k]['pheromone'] + 20;
                                        $idSpotList['id_spot'][$k] = $j + $k;

                                        $dosenTimeslotRelationList[$n]['sedang_mengajar'] = "t";
                                        $ruanganTimeslotRelationList[$l]['sedang_dipakai'] = "y";
                                    } else {
                                        unset($timeslotList);

                                        for ($m = 0; $m <= $k; $m++) {
                                            if ($dosenTimeslotRelationList[$n]['id_dosen'] == $id_dosen &&
                                                $dosenTimeslotRelationList[$n]['id_timeslot'] == $currentTimeslot) {
                                                $dosenTimeslotRelationList[$n - $m]['sedang_mengajar'] = "t";
                                            }
                                        }

                                        return getProperSpot(
                                            $spotList,
                                            $id_matkul,
                                            $sks,
                                            $ruanganTimeslotRelationList,
                                            $dosenTimeslotRelationList
                                        );
                                    }
                                }
                            }
                        } else {
                            unset($timeslotList);
                            return getProperSpot(
                                $spotList,
                                $id_matkul,
                                $sks,
                                $ruanganTimeslotRelationList,
                                $dosenTimeslotRelationList
                            );
                        }
                    }
                }

                // $timeslotIndex++;
                // $id_spot_index++;
            }
        }
    }

    return array(
        'spot_list' => $spotList,
        'id_spot_list' => $idSpotList,
        'id_dosen' => $id_dosen,
        'id_ruangan' => $id_ruangan,
        'timeslot_list' => $timeslotList,
        'ruangan_timeslot_relation_list' => $ruanganTimeslotRelationList,
    );
}

function getJalurList($semutId, & $spotList) {
    $jalurList = array();
    // $spotList = array();
    $ruanganTimeslotRelationList = getRuanganTimeslotRelationList();
    $dosenTimeslotRelationList = getDosenTimeslotRelationList();
    $matkulList = getMatkulList();
    $id_jalur = "";
    $index = 0;

    for ($j = 0; $j < count($matkulList); $j++) {
        $sks = $matkulList[$j]['sks'];
        $id_matkul = $matkulList[$j]['id_matkul'];

        $properSpot = getProperSpot(
            $spotList,
            $id_matkul,
            $sks,
            $ruanganTimeslotRelationList,
            $dosenTimeslotRelationList
        );

        $id_spot_list = $properSpot['id_spot_list']['id_spot'];
        $id_dosen = $properSpot['id_dosen'];
        $id_ruangan = $properSpot['id_ruangan'];
        $timeslotList = $properSpot['timeslot_list'];
        $ruanganTimeslotRelationList = $properSpot['ruangan_timeslot_relation_list'];

        $id_timeslot = array();
        $hari_timeslot = getHariFrom($timeslotList[0]['id_timeslot']);
        $durasi_timeslot = array();

        for ($k = 0; $k < count($timeslotList); $k++) {
            $id_timeslot[$k] = $timeslotList[$k]['id_timeslot'];
            $durasi_timeslot[$k] = $timeslotList[$k]['durasi_timeslot'];
        }

        $jalurList[$index] = array(
            // 'spot_list' => $spotList,
            'id_spot_list' => $id_spot_list,
            'id_matkul' => $id_matkul,
            'sks' => $sks,
            'id_dosen' => $id_dosen,
            'id_ruangan' => $id_ruangan,
            // 'id_timeslot' => $id_timeslot,
            'hari_timeslot' => $hari_timeslot,
            'durasi_timeslot' => $durasi_timeslot,
            'ruangan_timeslot_relation_list' => $ruanganTimeslotRelationList,
        );

        if ($j == count($matkulList) - 1 && $semutId == 100 - 1) {
            $spotList = getProperSpot(
                $spotList,
                $id_matkul,
                $sks,
                $ruanganTimeslotRelationList,
                $dosenTimeslotRelationList
            )['spot_list'];
        }

        $index++;
    }

    return $jalurList;
}

$spotList = getSpotList();
$ruanganList = getRuanganList();
$timeslotList = getTimeslotList();

$semutCount = 100;
$jalurListList = array();
for ($i = 0; $i < $semutCount; $i++) {
    $jalurList = getJalurList($i, $spotList);
    $jalurListList[$i] = $jalurList;
}

?>

<!doctype html>
<html>
<head>
    <title>REMM</title>
    <link rel="stylesheet" type="text/css" href="css/normalize.css"/>
    <link rel="stylesheet" type="text/css" href="css/grid.css"/>
    <link rel="stylesheet" type="text/css" href="css/html.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>

<body>
    <header class="main-header"><?=$appName?></header>

    <div class="grid-container main-content">
        <div class="grid-9">
            <div class="table-title">Tabel Mata Kuliah</div>
            <div class="matkul-table-container">
                <table class="matkul-table">
                    <thead>
                        <td>ID Mata Kuliah</td>
                        <td>Mata Kuliah</td>
                        <td>Jumlah SKS</td>
                        <td>Nama Dosen</td>
                    </thead>

                    <?php
                    $query = "SELECT m.id_matkul, m.nama_matkul, m.sks, d.nama_dosen FROM matkul m
                        JOIN and_dosen_matkul adm ON adm.id_matkul = m.id_matkul
                        JOIN dosen d ON d.id_dosen = adm.id_dosen
                        ORDER BY m.id_matkul ASC";
                    $exe = mysql_query($query);
                    while ($hasil = mysql_fetch_array($exe)) {
                        echo "<tbody>";
                        echo "<td>" . $hasil[0] . "</td>";
                        echo "<td>" . $hasil[1] . "</td>";
                        echo "<td>" . $hasil[2] . "</td>";
                        echo "<td>" . $hasil[3] . "</td>";
                        echo "</tbody>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="grid-3">
            <div class="table-title">Tabel Ruangan</div>
            <div class="ruangan-table-container">
                <table class="ruangan-table">
                    <thead>
                        <td>ID Ruangan</td>
                        <td>Nama Ruangan</td>
                    </thead>

                    <?php
                    for ($i = 0; $i < count($ruanganList); $i++) {
                        echo "<tbody>";
                        echo "<td>" . $ruanganList[$i]['id_ruangan'] . "</td>";
                        echo "<td>" . $ruanganList[$i]['id_ruangan'] . "</td>";
                        echo "</tbody>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div class="grid-container">
        <div class="grid-5">
            <div class="table-title">Tabel Timeslot</div>
            <div class="timeslot-table-container">
                <table class="timeslot-table">
                    <thead>
                        <td>ID Timeslot</td>
                        <td>Hari Timeslot</td>
                        <td>Durasi Timeslot</td>
                    </thead>

                    <?php
                    for ($i = 0; $i < count($timeslotList); $i++) {
                        echo "<tbody>";
                        echo "<td>" . $timeslotList[$i]['id_timeslot'] . "</td>";
                        echo "<td>" . $timeslotList[$i]['hari_timeslot'] . "</td>";
                        echo "<td>" . $timeslotList[$i]['durasi_timeslot'] . "</td>";
                        echo "</tbody>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="grid-7">
            <div class="table-title">Tabel Spot</div>
            <div class="spot-table-container">
                <table class="spot-table">
                    <thead>
                        <td>ID Spot</td>
                        <td>ID Matkul</td>
                        <td>ID Ruangan</td>
                        <td>ID Timeslot</td>
                        <td>Pheromone</td>
                    </thead>

                    <?php
                    // for ($k = 0; $k < count($jalurListList[count($jalurListList) - 1][count($jalurListList[count($jalurListList) - 1]) - 1]['spot_list']); $k++) {
                    //     $spotList = $jalurListList[count($jalurListList) - 1][count($jalurListList[count($jalurListList) - 1]) - 1]['spot_list'];
                    //     echo "<tbody>";
                    //     echo "<td>" . $spotList[$k]['id_spot'] . "</td>";
                    //     echo "<td>" . $spotList[$k]['id_matkul'] . "</td>";
                    //     echo "<td>" . $spotList[$k]['id_ruangan'] . "</td>";
                    //     echo "<td>" . $spotList[$k]['id_timeslot'] . "</td>";
                    //     echo "<td>" . $spotList[$k]['pheromone'] . "</td>";
                    //     echo "</tbody>";
                    // }

                    // for ($i = 0; $i < count($spots); $i++) {
                    //     echo "<tbody>";
                    //     echo "<td>" . $spots[$i]['id_spot'] . "</td>";
                    //     echo "<td>" . $spots[$i]['id_matkul'] . "</td>";
                    //     echo "<td>" . $spots[$i]['id_ruangan'] . "</td>";
                    //     echo "<td>" . $spots[$i]['id_timeslot'] . "</td>";
                    //     echo "<td>" . $spots[$i]['pheromone'] . "</td>";
                    //     echo "</tbody>";
                    // }

                    for ($i = 0; $i < count($spotList); $i++) {
                        echo "<tbody>";
                        echo "<td>" . $spotList[$i]['id_spot'] . "</td>";
                        echo "<td>" . $spotList[$i]['id_matkul'] . "</td>";
                        echo "<td>" . $spotList[$i]['id_ruangan'] . "</td>";
                        echo "<td>" . $spotList[$i]['id_timeslot'] . "</td>";
                        echo "<td>" . $spotList[$i]['pheromone'] . "</td>";
                        echo "</tbody>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="grid-12">
            <div class="table-title">Tabel Jalur</div>
            <div class="jalur-table-container">
                <table class="jalur-table">
                    <thead>
                        <td>ID Spot</td>
                        <td>ID Semut</td>
                        <td>ID Matkul</td>
                        <td>Nama Dosen</td>
                        <td>Jumlah SKS</td>
                        <td>ID Ruangan</td>
                        <!-- <td>ID Timeslot</td> -->
                        <td>Hari Timeslot</td>
                        <td>Durasi Timeslot</td>
                    </thead>

                    <?php
                    for ($j = 0; $j < count($jalurListList); $j++) {
                        $jalurList = $jalurListList[$j];
                        for ($i = 0; $i < count($jalurList); $i++) {
                            $id_spot = "";
                            $id_spot_list = $jalurList[$i]['id_spot_list'];
                            for ($k = 0; $k < count($id_spot_list); $k++) {
                                // $id_int = $index + 100000000 + 1;
                                // $id_spot = "SP" . substr($id_int, -8);
                                $id_spot = $id_spot . ("SP" . substr($id_spot_list[$k] + 100000000 + 1, -8)) . "<br>";
                            }

                            $id_dosen = $jalurList[$i]['id_dosen'];
                            $dosen = "SELECT nama_dosen FROM dosen WHERE id_dosen = '$id_dosen'";
                            $getDosen = mysql_query($dosen);
                            $nama_dosen = mysql_fetch_array($getDosen)[0];

                            // $id_timeslot_awal = $jalurList[$i]['id_timeslot'][0];
                            // $id_timeslot_akhir = $jalurList[$i]['id_timeslot'][count($jalurList[$i]['id_timeslot']) - 1];
                            // $id_timeslot = $id_timeslot_awal . " - " . $id_timeslot_akhir;

                            $hari_timeslot = $jalurList[$i]['hari_timeslot'];

                            $durasi_timeslot_awal = substr($jalurList[$i]['durasi_timeslot'][0], 0, 6);
                            $durasi_timeslot_akhir = substr($jalurList[$i]['durasi_timeslot'][count($jalurList[$i]['durasi_timeslot']) - 1], -6);
                            $durasi_timeslot = $durasi_timeslot_awal . " - " . $durasi_timeslot_akhir;

                            echo "<tbody>";
                            echo "<td>" . $id_spot . "</td>";
                            echo "<td>" . "SM" . substr($j + 1000 + 1, -3) . "</td>";
                            echo "<td>" . $jalurList[$i]['id_matkul'] . "</td>";
                            echo "<td>" . $nama_dosen . "</td>";
                            echo "<td>" . $jalurList[$i]['sks'] . "</td>";
                            echo "<td>" . $jalurList[$i]['id_ruangan'] . "</td>";
                            // echo "<td>" . $id_timeslot . "</td>";
                            echo "<td>" . $hari_timeslot . "</td>";
                            echo "<td>" . $durasi_timeslot . "</td>";
                            echo "</tbody>";
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <!-- <div class="grid-container">
        <div class="grid-12">
            <div class="table-title">Tabel Relasi Ruangan dan Timeslot</div>
            <div class="ruangan-timeslot-table-container">
                <table class="ruangan-timeslot-table">
                    <thead>
                        <td>ID Ruangan</td>
                        <td>ID Timeslot</td>
                        <td>Sedang Dipakai</td>
                    </thead>

                    <?php
                    $ruanganTimeslotRelationList = $jalurList[0]['ruangan_timeslot_relation_list'];
                    for ($i = 0; $i < count($ruanganTimeslotRelationList); $i++) {
                        echo "<tbody>";
                        echo "<td>" . $ruanganTimeslotRelationList[$i]['id_ruangan'] . "</td>";
                        echo "<td>" . $ruanganTimeslotRelationList[$i]['id_timeslot'] . "</td>";
                        echo "<td>" . $ruanganTimeslotRelationList[$i]['sedang_dipakai'] . "</td>";
                        echo "</tbody>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div> -->

    <footer class="main-footer">
        <p>Copyright &copy; <?=date('Y')?></p>
        <?=$appName?>
    </footer>
</body>
</html>
