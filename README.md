# klausurenweb.de - Intelligente Textbewertung in Moodle

klausurenweb.de ist ein Moodle-Plugin, das klausurenweb.de — ein automatisiertes, KI-unterstütztes Feedback- und Bewertungstool für sowohl handschriftliche als auch getippte Schülertexte — nahtlos in Ihre Moodle-Umgebung integriert. Durch die Nutzung der klausurenweb.de-API vereinfacht klausurenweb.de das Feedback zu Prüfungen, reduziert den Bewertungsaufwand und bietet zeitnahe Einblicke zur Verbesserung des Lernens und der Leistung der Schüler.

Dieses Plugin ermöglicht die Integration von klausurenweb.de in Moodle. Moodle-Nutzer können damit:

- Als Administrator einen Schul-API-Schlüssel speichern
- Als Lehrkraft im Kurs einen eigenen API-Schlüssel hinterlegen
- klausurenweb-Lerncodes bei Aufgaben hinterlegen
- Hochgeladene Schülerarbeiten als Einreichungen an klausurenweb.de zur Bewertung übermitteln

## Voraussetzungen

- Moodle 4.1 oder höher
- Ein aktiver klausurenweb.de-Account mit API-Zugang
- API-Schlüssel für die Schule und/oder Lehrkräfte

## Installation

Es gibt zwei Möglichkeiten, dieses Plugin zu installieren:

### 1. Installation über die Moodle-Weboberfläche

1. Packen Sie den gesamten Inhalt des Verzeichnisses `mod/engelbrain` in eine ZIP-Datei.
2. Melden Sie sich als Administrator in Ihrer Moodle-Instanz an.
3. Gehen Sie zu **Website-Administration** > **Plugins** > **Plugin installieren**.
4. Ziehen Sie die ZIP-Datei in den Bereich "ZIP-Paket" oder wählen Sie die Datei über den Dateiauswahldialog aus.
5. Klicken Sie auf "Plugin installieren aus ZIP-Datei".
6. Folgen Sie den Anweisungen auf dem Bildschirm, um die Installation abzuschließen.

### 2. Manuelle Installation

1. Laden Sie den Inhalt des Verzeichnisses `mod/engelbrain` in das Verzeichnis `mod/engelbrain` Ihrer Moodle-Installation hoch.
2. Melden Sie sich als Administrator an.
3. Besuchen Sie die Seite **Benachrichtigungen**, um die Installation abzuschließen.

## Konfiguration

### Administrator-Konfiguration

1. Gehen Sie zu **Website-Administration** > **Plugins** > **Aktivitäten** > **klausurenweb.de**.
2. Geben Sie den Schul-API-Schlüssel ein, den Sie von klausurenweb.de erhalten haben.
3. Überprüfen Sie die API-Endpunkt-URL (sollte standardmäßig auf `https://klausurenweb.de/api/v1` gesetzt sein).

### Lehrkraft-Konfiguration

1. Erstellen Sie einen neuen Kurs oder öffnen Sie einen bestehenden Kurs.
2. Aktivieren Sie den Bearbeitungsmodus.
3. Klicken Sie auf "Aktivität oder Material hinzufügen" und wählen Sie "klausurenweb.de-Aufgabe".
4. Geben Sie einen Namen und eine Beschreibung für die Aufgabe ein.
5. Geben Sie Ihren persönlichen Lehrer-API-Schlüssel ein (optional, wenn bereits ein Schul-API-Schlüssel konfiguriert ist).
6. Geben Sie den Lerncode für die Aufgabe ein, der in klausurenweb.de erstellt wurde.
7. Konfigurieren Sie die weiteren Optionen nach Bedarf und speichern Sie die Aktivität.

## Nutzung

### Für Lehrkräfte

1. Erstellen Sie eine klausurenweb.de-Aufgabe in Ihrem Kurs.
2. Konfigurieren Sie den entsprechenden Lerncode und API-Schlüssel.
3. Studierende können nun Einreichungen vornehmen.
4. Sie können die Einreichungen in der Aktivität einsehen und das Feedback von klausurenweb.de anzeigen.

### Für Studierende

1. Öffnen Sie die klausurenweb.de-Aufgabe in Ihrem Kurs.
2. Laden Sie Ihre Arbeit hoch oder geben Sie Ihren Text direkt ein.
3. Klicken Sie auf "Abgeben".
4. Die Einreichung wird automatisch an klausurenweb.de gesendet und dort bewertet.

## Fehlerbehebung

- Stellen Sie sicher, dass die API-Schlüssel korrekt sind und über ausreichende Berechtigungen verfügen.
- Überprüfen Sie, ob der Lerncode gültig ist und zu einer aktiven Aufgabe in klausurenweb.de gehört.
- Bei Problemen mit der API-Verbindung, prüfen Sie die Server-Logs auf mögliche Fehler.

## Support

Bei Fragen oder Problemen wenden Sie sich bitte an:

- E-Mail: hallo@panomity.de
- Website: https://klausurenweb.de

## Lizenz

Dieses Plugin ist unter der GNU GPL v3 lizenziert. Siehe die mitgelieferte LICENSE-Datei für Details. 