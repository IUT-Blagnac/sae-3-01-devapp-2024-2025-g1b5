package sae.appli;

import java.nio.file.*;

import sae.view.AlarmPopUpController;

import java.io.IOException;

import java.time.Instant;

public class FlagFileWatcher {

    private static final String FLAG_FILE_PATH = "Iot/trigger.flag";
    private static Instant lastModified = Instant.MIN; // Dernière modification connue

    public void startWatching() {
        new Thread(() -> {
            try {
                Path path = Paths.get(FLAG_FILE_PATH).getParent();
                WatchService watchService = FileSystems.getDefault().newWatchService();
                path.register(watchService, StandardWatchEventKinds.ENTRY_MODIFY);

                System.out.println("Surveillance du fichier " + FLAG_FILE_PATH);

                while (true) {
                    WatchKey key;
                    try {
                        key = watchService.take();
                    } catch (InterruptedException e) {
                        System.out.println("Watcher interrompu.");
                        return;
                    }

                    for (WatchEvent<?> event : key.pollEvents()) {
                        if (event.kind() == StandardWatchEventKinds.ENTRY_MODIFY &&
                                event.context().toString().equals(Paths.get(FLAG_FILE_PATH).getFileName().toString())) {

                            Instant now = Instant.now();
                            if (lastModified.plusSeconds(1).isBefore(now)) { // Limite les alertes à 1 seconde d'écart
                                lastModified = now; // Met à jour la dernière modification
                                System.out.println("Le fichier a été modifié !");
                                showAlarmPopUp();
                            }
                        }
                    }

                    boolean valid = key.reset();
                    if (!valid) {
                        break;
                    }
                }

            } catch (IOException e) {
                e.printStackTrace();
            }
        }).start();
    }

    private void showAlarmPopUp() {
        AlarmPopUpController alarmPopUpController = new AlarmPopUpController();
        alarmPopUpController.showAlarmPopUp();
    }
}
