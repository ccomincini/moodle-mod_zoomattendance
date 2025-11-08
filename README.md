# Zoom Attendance per Moodle v1.0.0

[![Versione](https://img.shields.io/badge/versione-1.0.0-brightgreen.svg)](https://github.com/ccomincini/moodle-mod_zoomattendance)
[![Moodle](https://img.shields.io/badge/Moodle-4.0%2B-blue.svg)](https://moodle.org)
[![Licenza](https://img.shields.io/badge/licenza-GPL%20v3-orange.svg)](https://www.gnu.org/licenses/gpl-3.0.html)
[![Stabilità](https://img.shields.io/badge/stabilit%C3%A0-STABLE-green.svg)]()

Modulo di attività avanzato per Moodle che traccia la partecipazione dalle riunioni Zoom con aggregazione corretta delle presenze, matching intelligente degli utenti e interfaccia ottimizzata per la gestione dei registri.

## 🎯 Caratteristiche Principali

### ✨ Funzionalità Core
- **Importazione Dati Zoom API**: Scarica automaticamente i dati di partecipazione dalle riunioni Zoom
- **Aggregazione Intelligente Presenze**: Deduplicazione e merge di sessioni multiple (multi-device, riconnessioni)
- **Clipping Temporale Automatico**: Calcolo durata presenze limitato al range orario del registro
- **Matching Automatico Utenti**: Algoritmo multi-fase che associa partecipanti Zoom agli utenti iscritti al corso
- **Assegnazione Manuale Guidata**: Interfaccia per disambiguazione record non identificati
- **Computo Soglia Presenza**: Verifica automatica percentuali di partecipazione e completamento attività
- **Export Dati**: Esportazione XLSX con statistiche e dettagli presenze

### 🔧 Gestione Presenze Avanzata
- **Merge Sessioni**: Unificazione automatica di più connessioni dello stesso utente nel range temporale
- **Scarto Duplicati**: Eliminazione automatica sovrapposizioni e doppioni su join/leave multipli
- **Clipping Range Registro**: Validazione che nessuna presenza superi l'intervallo orario configurato
- **Calcolo Percentuali Accurate**: Base di calcolo garantita dal merge validato
- **Log Aggregazione**: Tracciamento dettagliato di ogni operazione per audit e debug

### 🎯 Matching Intelligente
- **Pattern Email**: Riconoscimento automatico corrispondenze email (nome.cognome@domain, n.cognome@domain)
- **Parsing Nomi**: Estrazione intelligente da ID Zoom con normalizzazione accenti e caratteri speciali
- **Logica Anti-Ambiguità**: Esclude suggerimenti falsi positivi quando multiple corrispondenze
- **Nomi Invertiti**: Gestione automatica inversioni cognome/nome nei dati Zoom
- **Classe Matcher Six-Phase**: Sistema avanzato di matching testato su plugin Microsoft Teams

### 💾 Gestione Dati Robusto
- **Aggregazione Gerarchica**: Raggruppa record raw → merge temporale → associazione utente → somma finale
- **Validazione Intervalli**: Ogni intervallo join/leave validato e clippato al range registro
- **Deduplicazione Multi-Livello**: Elimina doppioni sia sul grezzo che su aggregati finali
- **Coerenza Dati**: Garantisce che durata finale = somma intervalli senza buchi/sovrapposizioni

## 📋 Requisiti di Sistema

### Requisiti Minimi
- **Moodle**: 4.0+ (testato fino a 4.3)
- **PHP**: 7.4+ con supporto cURL, JSON e SPL
- **Database**: MySQL 5.7+ / MariaDB 10.2+ / PostgreSQL 10+
- **Plugin mod_zoom**: Versione 2022041900 o superiore, attivo e configurato
- **Memoria PHP**: 256MB+ raccomandati per import grandi dataset

### Dipendenze
- `mod_zoom` >= 2022041900 (Plugin Zoom ufficiale per Moodle)
- Libreria PHPSpreadsheet (inclusa)

## 🚀 Installazione

### 1. Download del Plugin

Esegui il clone nella cartella mod di Moodle:

```bash
cd /path/to/moodle/mod/
git clone https://github.com/ccomincini/moodle-mod_zoomattendance.git zoomattendance
cd zoomattendance
```

### 2. Completamento Installazione

1. Accedi a Moodle come amministratore
2. Naviga a **Amministrazione del sito → Notifiche**
3. Segui la procedura guidata di installazione automatica
4. Verifica che mod_zoom sia configurato (Amministrazione → Plugin → Moduli attività → Zoom)

### 3. Configurazione Zoom API

Assicurati che il plugin mod_zoom disponga di:
- Zoom API Key valida
- Zoom API Secret valido
- Credenziali di autenticazione corrette

Per configurare: **Amministrazione → Plugin → Moduli attività → Zoom → Impostazioni**

## 📖 Guida Utilizzo Rapida

### Creazione Attività Zoom Attendance

1. Nel corso, aggiungi attività selezionando **Zoom Attendance**
2. **Configura Parametri**:
   - Seleziona il meeting Zoom da tracciare
   - Imposta data/ora inizio registro (ad es. 14:30)
   - Imposta data/ora fine registro (ad es. 16:30)
   - Definisci percentuale minima di partecipazione richiesta (90%)
   - Salva

### Importazione Dati Presenze

1. Nella vista dell'attività, clicca **Recupera dati Zoom**
2. Attendi completamento import (mostra X record di partecipazione importati)
3. Il sistema automaticamente:
   - Scarica i partecipanti dal meeting Zoom
   - Aggrega e clippa gli intervalli al range registro
   - Deduplica sessioni multiple
   - Calcola durate corrette per ogni partecipante
   - Associa automaticamente agli utenti iscritti

### Gestione Assegnazioni Manuali

1. Dalla vista attività, clicca **Gestisci non assegnati**
2. Visualizza record di partecipazione non automaticamente associati
3. Per ciascun record:
   - Leggi il suggerimento di matching (se disponibile)
   - Clicca **Applica suggerimento** per accettare
   - Oppure seleziona manualmente l'utente da dropdown
4. Il sistema riunifica automaticamente le presenze per utente

### Verifica Risultati

Nella tabella principale vedrai per ogni utente:
- **Cognome/Nome**: Nominativo Moodle
- **Durata partecipazione**: Tempo aggregato nel range registro (es. 2h 15m)
- **% presenza**: Calcolata su durata registro (es. 112%)
- **Superamento soglia**: ✓ Sì se >= percentuale richiesta

### Export Dati

- **Export CSV**: Clicca "Esporta in CSV" per download rapido
- **Export XLSX**: Clicca "Esporta in Excel" per file formattato con statistiche
- I dati esportati rispecchiano esattamente le presenze calcolate e aggregate

## 🔍 Tecnologie Implementate

### Backend (PHP)
- **interval_merger.php**: Merge intervalli, clipping, deduplica temporale
- **zoom_report_handler.php**: Orchestrazione import, aggregazione, calcolo durate
- **six_phase_matcher.php**: Matching avanzato multi-fase utenti
- **performance_data_handler.php**: Gestione dati ottimizzata per performance

### Frontend (JavaScript/AMD)
- **unassigned_manager.js**: Interfaccia gestione non assegnati con AJAX
- **Paginazione intelligente**: Navigazione fluida grandi dataset

### Database
- Tabelle: zoomattendance, zoom_meeting_details, zoom_meeting_participants, zoomattendancedata
- Indici ottimizzati su meeting_id, userid, join_time, leave_time

## 🧪 Verifica Funzionamento

**Test di base:**
1. Crea meeting Zoom di test (durata 2 ore)
2. Partecipa con 2-3 account diversi
3. Disconettiti e riconnettiti (test multi-sessione)
4. Nel plugin Moodle: Recupera dati Zoom
5. Verifica: durate dovrebbero = intervallo configurato registro (non superiore)

**Test aggregazione:**
- Se un utente si connette 3 volte (es. 30min, 45min, 20min = 95min totali)
- Il plugin deve riportare ~ 95min (o meno se parte fuori range registro)

## 🐛 Debug e Troubleshooting

### Presenze superiori al range registro
- Verifica che start/end DateTime del registro siano impostati correttamente
- Controlla log: sono presenti i filtri di clipping?
- Rivedi le sessioni raw importate vs quelle aggregate nel database

### Record non associati automaticamente
- Controlla che i nomi/email nei partecipanti Zoom matchino con utenti Moodle
- Usa il sistema di assegnazione manuale per disambiguazione
- I dati verranno poi unificati nella somma presenze finale

### Performance lenta con molti record
- Verifica memory_limit PHP (almeno 256MB)
- Controlla che il database non abbia indici mancanti
- Per dataset > 5000 record, considera import in batch

## 📚 Struttura Cartelle

```
zoomattendance/
├── classes/
│   ├── interval_merger.php           # Merge e deduplica intervalli
│   ├── zoom_report_handler.php       # Orchestrazione import/aggregazione
│   ├── six_phase_matcher.php         # Matching automatico utenti
│   ├── performance_data_handler.php  # Gestione dati ottimizzata
│   ├── suggestion_engine.php         # Motore suggerimenti match
│   └── [altri handler e utilità]
├── amd/src/
│   └── unassigned_manager.js         # Interfaccia AJAX non assegnati
├── lang/
│   ├── it/zoomattendance.php         # Localizzazione italiano
│   └── en/zoomattendance.php         # Localizzazione inglese
├── styles/
│   └── [fogli di stile CSS]
├── db/
│   ├── install.xml                   # Schema database
│   ├── access.php                    # Definizioni capability
│   └── upgrade.php                   # Script upgrade versioni
├── fetch_attendance.php              # Endpoint import dati Zoom
├── manage_unassigned.php             # Interfaccia gestione manuale
├── view.php                          # Vista principale registro
├── export_attendance_xlsx.php        # Export Excel
└── README.md                         # Questo file
```

## 🔐 Sicurezza

- **Capability-based**: Accesso controllato tramite permessi Moodle
- **Input Validation**: Sanitizzazione input via parametri Moodle
- **SQL Injection Safe**: Utilizzo prepared statements DB
- **CSRF Protection**: Token di sessione Moodle
- **Output Encoding**: Escape output per HTML/JavaScript

## 📝 Log e Monitoraggio

I log di import/aggregazione sono disponibili:
- **File**: `./zoomlog.txt` (nella cartella plugin)
- Contengono: timestamp, operazioni import, aggregazioni, errori
- Utili per audit e debug anomalie

## 🤝 Contributi e Supporto

### Segnalazione Bug
Descrivi il problema con:
- Versione Moodle e plugin
- Steps per riprodurre
- Log di errore (se disponibile)

### Contatti
- **Email**: [translate:c.comincini@invisiblefarm.com]
- **Maintainer**: Carlo Comincini
- **Azienda**: Invisiblefarm srl

## 📄 Licenza

**GNU General Public License v3.0 o successiva**

Questo plugin è software libero. Puoi ridistribuirlo e/o modificarlo secondo i termini della licenza GPL v3.

## 🎉 Changelog

### v1.0.0 (Novembre 2025)
- **Nuovo**: Aggregazione presenze con clipping temporale
- **Nuovo**: Merge automatico sessioni multiple
- **Nuovo**: Deduplicazione intervalli sovrapposti
- **Nuovo**: Calcolo durata garantito nel range registro
- **Miglioramento**: Performance ottimizzate per 1000+ partecipanti
- **Miglioramento**: Matching automatico multi-fase
- **Miglioramento**: Interfaccia gestione non assegnati

---

**📦 Versione**: 1.0.0
**🎯 Compatibilità**: Moodle 4.0+
**📅 Ultimo Aggiornamento**: Novembre 2025
**👨‍💻 Maintainer**: Carlo Comincini
**🏢 Publisher**: Invisiblefarm srl (invisiblefarm.com)
