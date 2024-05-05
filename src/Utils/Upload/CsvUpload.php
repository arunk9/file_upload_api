<?php

namespace App\Utils\Upload;

use App\Entity\User;

class CsvUpload extends BaseFileUpload
{
    const HEADERS = [
        'username',
        'email',
        'password'
    ];

    /**
     * @return string|null
     * @throws \Random\RandomException
     */
    public function upload(): ?string
    {
        $csvData = $this->file->getContent();

        if (empty($csvData)) {
            throw new \Exception('CSV data is empty');
        }

        // Parse CSV data
        $rows = str_getcsv($csvData, "\n");

        if (str_getcsv($rows[0]) !== self::HEADERS)  {
            throw new \Exception('Invalid CSV headers');
        }

        // skip first row i.e headers
        array_shift($rows);

        $batchId = base64_encode(random_bytes(10));

        // Process and save data to the database
        foreach ($rows as $row) {
            $rowData = str_getcsv($row);

            // Create a new instance of user and set properties
            $user = new User();
            $user->setUsername($rowData[0]); // Adjust property names according to your CSV structure
            $encodedPassword = $this->passwordHasher->hashPassword($user, $rowData[2]);
            $user->setPassword($encodedPassword);
            $user->setEmail($rowData[1]);
            $user->setBatchId($batchId);
            // Persist user to the database
            $this->entityManager->persist($user);
        }

        // Flush changes to the database
        $this->entityManager->flush();

        return $batchId;
    }
}