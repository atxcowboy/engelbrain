_This README is available in both English and German._

## Contents
- [English](#English)
- [Deutsch](#Deutsch)

# English
# Engelbrain – Intelligent Text Assessment in Moodle

Engelbrain is a Moodle plugin that seamlessly integrates klausurenweb.de — an automated, AI-assisted feedback and assessment tool for both handwritten and typed student texts — via its [API](https://klausurenweb.de/api-docs) into your Moodle environment. The name combines "Engel" (German for “angel”) and "brain" (English for intellect), symbolizing a supportive yet intelligent presence in education and assessment. By using the klausurenweb.de API, Engelbrain simplifies exam feedback, reduces the workload for grading, and provides timely insights for improving student learning and performance.

This plugin enables the integration of klausurenweb.de into Moodle. Moodle users can:

- Save a school API key as an administrator
- Enter their own API key as a teacher in the course
- Store klausurenweb learning codes in assignments
- Submit uploaded student work to klausurenweb.de for grading

## Requirements

- Moodle 4.1 or higher
- An active klausurenweb.de account with API access
- API key for the school and/or teachers

## Installation

There are two ways to install this plugin:

### 1. Installation via the Moodle Web Interface

1. Pack the entire contents of the `mod/engelbrain` directory into a ZIP file.
2. Log in to your Moodle instance as an administrator.
3. Go to **Site administration** > **Plugins** > **Install plugins**.
4. Drag the ZIP file into the "ZIP package" area or select the file via the file selection dialog.
5. Click on "Install plugin from ZIP file".
6. Follow the on-screen instructions to complete the installation.

### 2. Manual Installation

1. Upload the contents of the `mod/engelbrain` directory to the `mod/engelbrain` directory of your Moodle installation.
2. Log in as an administrator.
3. Visit the **Notifications** page to complete the installation.

## Configuration

### Administrator Configuration

1. Go to **Site administration** > **Plugins** > **Activities** > **Engelbrain**.
2. Enter the school API key you received from klausurenweb.de.
3. Check the API endpoint URL (which should be set by default to `https://klausurenweb.de/api/v1`).

### Teacher Configuration

1. Create a new course or open an existing course.
2. Enable editing mode.
3. Click **Add an activity or resource** and select **Engelbrain Assignment**.
4. Enter a name and a description for the assignment.
5. Enter your personal teacher API key (optional if a school API key is already configured).
6. Enter the learning code for the assignment that was created in klausurenweb.de.
7. Configure any additional options as needed and save the activity.

## Usage

### For Teachers

1. Create an Engelbrain assignment in your course.
2. Configure the corresponding learning code and API key.
3. Students can now make submissions.
4. You can view submissions in the activity and see the feedback from klausurenweb.de.

### For Students

1. Open the Engelbrain assignment in your course.
2. Upload your work or enter your text directly.
3. Click **Submit**.
4. The submission is automatically sent to klausurenweb.de for assessment.

## Troubleshooting

- Make sure the API keys are correct and have sufficient permissions.
- Check whether the learning code is valid and belongs to an active assignment in klausurenweb.de.
- If you experience issues with the API connection, review the server logs for possible errors.

## Support

If you have any questions or issues, please contact:

- Email: hallo@panomity.de
- Website: [https://klausurenweb.de](https://klausurenweb.de)

## License

This plugin is licensed under the GNU GPL v3. See the accompanying LICENSE file for details.

# Deutsch
# Engelbrain - Intelligente Textbewertung in Moodle

Engelbrain ist ein Moodle-Plugin, das klausurenweb.de — ein automatisiertes, KI-unterstütztes Feedback- und Bewertungstool für sowohl handschriftliche als auch getippte Schülertexte — nahtlos in Ihre Moodle-Umgebung integriert. Der Name kombiniert "Engel" (Deutsch) und "brain" (Englisch für Intellekt) und symbolisiert eine unterstützende, aber intelligente Präsenz in Bildung und Bewertung. Durch die Nutzung der klausurenweb.de-API vereinfacht Engelbrain das Feedback zu Prüfungen, reduziert den Bewertungsaufwand und bietet zeitnahe Einblicke zur Verbesserung des Lernens und der Leistung der Schüler.

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

1. Gehen Sie zu **Website-Administration** > **Plugins** > **Aktivitäten** > **Engelbrain**.
2. Geben Sie den Schul-API-Schlüssel ein, den Sie von klausurenweb.de erhalten haben.
3. Überprüfen Sie die API-Endpunkt-URL (sollte standardmäßig auf `https://klausurenweb.de/api/v1` gesetzt sein).

### Lehrkraft-Konfiguration

1. Erstellen Sie einen neuen Kurs oder öffnen Sie einen bestehenden Kurs.
2. Aktivieren Sie den Bearbeitungsmodus.
3. Klicken Sie auf "Aktivität oder Material hinzufügen" und wählen Sie "Engelbrain-Aufgabe".
4. Geben Sie einen Namen und eine Beschreibung für die Aufgabe ein.
5. Geben Sie Ihren persönlichen Lehrer-API-Schlüssel ein (optional, wenn bereits ein Schul-API-Schlüssel konfiguriert ist).
6. Geben Sie den Lerncode für die Aufgabe ein, der in klausurenweb.de erstellt wurde.
7. Konfigurieren Sie die weiteren Optionen nach Bedarf und speichern Sie die Aktivität.

## Nutzung

### Für Lehrkräfte

1. Erstellen Sie eine Engelbrain-Aufgabe in Ihrem Kurs.
2. Konfigurieren Sie den entsprechenden Lerncode und API-Schlüssel.
3. Studierende können nun Einreichungen vornehmen.
4. Sie können die Einreichungen in der Aktivität einsehen und das Feedback von klausurenweb.de anzeigen.

### Für Studierende

1. Öffnen Sie die Engelbrain-Aufgabe in Ihrem Kurs.
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
