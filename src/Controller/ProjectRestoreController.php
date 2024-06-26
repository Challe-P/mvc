<?php

namespace App\Controller;

use App\Service\InterfaceHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * A controller class for the library route.
 */
class ProjectRestoreController extends AbstractController
{
    /**
     * Route to restore the database from the backup file.
     */
    #[Route('proj/restore', name: 'restoreDatabase')]
    public function restoreDatabase(
        InterfaceHelper $interface,
        ParameterBagInterface $params,
        Request $request
    ): Response {
        $rootPath = $request->get('rootPath');
        $rootPath = $rootPath ?? $params->get('kernel.project_dir');
        if (!is_string($rootPath)) {
            return new Response('Root path is not string.', 500);
        }

        $databasePath = $rootPath . '/var/data.db';
        $backupFilePath = $rootPath . '/var/backup.bak';

        // Check if the database file exists and deletes it
        if ($interface->fileExists($databasePath)) {
            if (!$interface->unlink($databasePath)) {
                return new Response('Failed to delete the database file.', 500);
            }
        }

        // Restore the database from the backup file using the system command
        $restoreCommand = "sqlite3 $databasePath < $backupFilePath";
        $output = [];
        $returnVar = null;
        $interface->exec($restoreCommand, $output, $returnVar);

        if ($returnVar !== 0) {
            return new Response('Failed to restore the database from backup.', 500, $output);
        }

        return $this->redirectToRoute('projApi');
    }
}
