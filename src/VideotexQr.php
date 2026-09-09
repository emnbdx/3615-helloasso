<?php

namespace App;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;

final class VideotexQr
{
    public static function render(string $data, int $originCol, int $originRow, int $maxCols, int $maxRows): string
    {
        $options = new QROptions([
            'eccLevel' => EccLevel::L,
            'addQuietzone' => true,
            'quietzoneSize' => 1,
        ]);
        $matrix = (new QRCode($options))->addByteSegment($data)->getQRMatrix();
        $n = $matrix->getSize();
        $modules = [];
        for ($y = 0; $y < $n; $y++) {
            $row = [];
            for ($x = 0; $x < $n; $x++) {
                $row[] = $matrix->check($x, $y);
            }
            $modules[] = $row;
        }

        $maxPxW = max(2, $maxCols * 2);
        $maxPxH = max(3, $maxRows * 3);
        $scale = (int) max(1, min(intdiv($maxPxW, $n), intdiv($maxPxH, $n)));
        $pxW = $n * $scale;
        $pxH = $n * $scale;
        $charW = (int) ceil($pxW / 2);
        $charH = (int) ceil($pxH / 3);
        $col = $originCol + (int) max(0, intdiv($maxCols - $charW, 2));
        $row = $originRow + (int) max(0, intdiv($maxRows - $charH, 2));

        $vdt = VDT_SZNORM . VDT_G0;
        for ($cy = 0; $cy < $charH; $cy++) {
            $line = '';
            for ($cx = 0; $cx < $charW; $cx++) {
                $line .= self::mosaicChar($modules, $scale, $n, $cx * 2, $cy * 3);
            }
            $vdt .= \MiniPavi\MiniPaviCli::setPos($col, $row + $cy);
            $vdt .= VDT_G1 . VDT_STARTUNDERLINE . VDT_BGWHITE . VDT_TXTBLACK;
            $vdt .= $line;
            $vdt .= VDT_STOPUNDERLINE . VDT_G0;
        }

        return $vdt;
    }

    private static function mosaicChar(array $modules, int $scale, int $n, int $px, int $py): string
    {
        $bits = [1, 2, 4, 8, 16, 64];
        $offsets = [[0, 0], [1, 0], [0, 1], [1, 1], [0, 2], [1, 2]];
        $code = 0x20;
        foreach ($offsets as $i => [$dx, $dy]) {
            $x = intdiv($px + $dx, $scale);
            $y = intdiv($py + $dy, $scale);
            if ($x >= 0 && $y >= 0 && $x < $n && $y < $n && $modules[$y][$x]) {
                $code += $bits[$i];
            }
        }
        return chr($code);
    }
}
