<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

final class ProjectManager
{
    public function createJsonTrainingData(int $dataLength = 50): int
    {
        $datas = [];
        $encoder = new JsonEncoder();
        $filesystem = new Filesystem();
        $randFloat = fn (float $min = 0, float $max = 1): float => $min + mt_rand() / mt_getrandmax() * ($max - $min);
        $dataLength = min($dataLength, 1000);

        foreach (range(1, $dataLength) as $i) {
            $datas[] = [
                'input' => [
                    'budgetPercentage' => round($randFloat(0, 20 > rand(0, 100) ? 2 : 1), 2),
                    'daysPercentage' => 0 === rand(0, 1) ? round($randFloat(), 2) : null,
                    'riskPercentage' => 25 > rand(0, 100) ? 1 : round($randFloat(), 2),
                    'milestonePercentage' => 25 > rand(0, 100) ? 1 : round($randFloat(), 2),
                ],
                'output' => [
                    'Expected output here.' => 1,
                ],
            ];
        }

        if (!$filesystem->exists('data')) {
            $filesystem->mkdir('data');
        }

        $json = $encoder->encode($datas, JsonEncoder::FORMAT);
        $filesystem->dumpFile('data/brainData.json', $json);

        return $dataLength;
    }
}
