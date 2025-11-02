cd /media/dati/ivf/docker/izsumtestfad/var/www/moodle/mod/zoomattendance

# Backup del README attuale (opzionale)
cp README.md README_old_teams.md

# Crea nuovo README.md
cat > README.md << 'EOF'
# Zoom Attendance per Moodle v1.0.0

[![Versione](https://img.shields.io/badge/versione-1.0.0-brightgreen.svg)](https://github.com/ccomincini/moodle-mod_zoomattendance)
[![Moodle](https://img.shields.io/badge/Moodle-3.9%2B-blue.svg)](https://moodle.org)
[![Licenza](https://img.shields.io/badge/licenza-GPL%20v3-orange.svg)](https://www.gnu.org/licenses/gpl-3.0.html)
[![Stabilità](https://img.shields.io/badge/stabilit%C3%A0-STABLE-green.svg)]()

Modulo di attività avanzato per Moodle che traccia la partecipazione dalle riunioni Zoom con matching intelligente degli utenti, sistema di filtri avanzato e interfaccia ottimizzata.

## 🎯 Caratteristiche Principali

### ✨ Funzionalità Core
- **Integrazione Zoom Automatica**: Importa i dati di partecipazione direttamente da Zoom tramite API
- **Matching Intelligente degli Utenti**: Algoritmo avanzato che associa automaticamente i partecipanti Zoom agli utenti Moodle
- **Sistema di Filtri Avanzato**: Filtra e gestisci i record non assegnati con precisione
- **Interfaccia Performance Ottimizzata**: Gestisce efficacemente oltre 1000+ partecipanti
- **Export XLSX**: Esportazione avanzata dei dati di presenza in formato Excel

### 🔍 Sistema di Filtri
- **Filtro "Suggerimenti Nome"**: Visualizza solo i record con suggerimenti basati su similarità del nome
- **Filtro "Suggerimenti Email"**: Mostra record con suggerimenti basati su pattern email riconosciuti
- **Filtro "Senza Suggerimenti"**: Evidenzia record che richiedono assegnazione manuale
- **Paginazione Intelligente**: Navigazione fluida attraverso grandi dataset
- **Aggiornamento Real-time**: Contatori e statistiche aggiornati dinamicamente

### 🧠 Capacità di Matching
- **Pattern Email Intelligenti**: Riconosce pattern email comuni (nome.cognome@domain, ncognome@domain, etc.)
- **Parsing Avanzato dei Nomi**: Estrae nomi da ID Zoom con rumore (titoli, organizzazioni)
- **Logica Anti-Ambiguità**: Previene suggerimenti falsi positivi per match multipli
- **Supporto Nomi Invertiti**: Gestisce scambi cognome/nome nei campi
- **Caratteri Internazionali**: Normalizza accenti e caratteri speciali automaticamente

### ⚡ Ottimizzazioni Performance
- **Query Database Ottimizzate**: Performance elevate con query indicizzate
- **Interfaccia AJAX**: Aggiornamenti in tempo reale senza ricaricamenti
- **Operazioni Bulk**: Elabora centinaia di assegnazioni simultaneamente
- **Gestione Memoria**: Utilizzo memoria ottimizzato per grandi dataset

## 📋 Requisiti di Sistema

### Requisiti Minimi
- **Moodle**: 3.9+ (testato fino alla 4.0)
- **PHP**: 7.4+ con supporto cURL e JSON
- **Database**: MySQL 5.7+ o PostgreSQL 10+
- **Plugin Zoom**: mod_zoom attivo e configurato
- **Memoria PHP**: Minimum 128MB, raccomandati 256MB per grandi dataset

### Dipendenze
- `mod_zoom` >= 2022041900 (Plugin Zoom per Moodle)

## 🚀 Installazione

### 1. Download e Estrazione

Nella cartella dei moduli attività di Moodle ( /path/to/moodle/mod/ ) esegui un clone del repository.


### 2. Completamento Installazione

1. Vai in **Amministrazione del sito → Notifiche**
2. Segui la procedura guidata di installazione
3. Verifica che mod_zoom sia configurato correttamente

### 3. Configurazione Zoom

Assicurati che il plugin mod_zoom sia configurato con:
- **Zoom API Key** valida
- **Zoom API Secret** valido
- **Zoom JWT Token** (se richiesto)

## 📖 Guida Utilizzo

### Creazione di un'Attività

1. **Aggiungi Attività**: Seleziona "Zoom Attendance" nel corso
2. **Configura Meeting**: Seleziona il meeting Zoom esistente
3. **Imposta Parametri**:
   - Percentuale minima di partecipazione richiesta
   - Durata prevista per il tracking del completamento
   - Criteri di valutazione

### Importazione Dati Partecipazione

1. **Avvia Import**: Clicca "Recupera dati Zoom" nella vista attività
2. **Connessione Automatica**: Il sistema si connette alle API Zoom
3. **Verifica Risultati**: Controlla i dati importati e le statistiche

### Gestione Record Non Assegnati

1. **Accedi alla Gestione**: Clicca "Gestisci non assegnati"
2. **Utilizza i Filtri**:
   - **Tutti i record**: Visualizzazione completa
   - **Suggerimenti nome**: Solo record con match basati su nome
   - **Suggerimenti email**: Solo record con match basati su email
   - **Senza suggerimenti**: Record che richiedono assegnazione manuale
3. **Applica Assegnazioni**:
   - **Singole**: Clicca "Applica suggerimento" per singoli record
   - **Bulk**: Seleziona multipli record e usa "Applica selezionati"
   - **Manuali**: Usa il dropdown per assegnazioni personalizzate

### Export dei Dati

- **Export CSV**: Download rapido dei dati di presenza
- **Export XLSX**: Export avanzato con formattazione Excel
- **Filtri Export**: Applica filtri prima dell'export per dati specifici

## 🛠️ Sviluppo e Contributi

### Struttura Codice

- **classes/**: Classi PHP core del sistema
- **amd/**: JavaScript AMD per interfaccia dinamica
- **styles/**: Fogli di stile CSS
- **lang/**: File di localizzazione (IT/EN)

### Testing

Per testare il plugin:
1. Crea un'attività Zoom Attendance
2. Importa dati da un meeting Zoom esistente
3. Verifica il sistema di matching automatico
4. Testa l'export XLSX

## 📄 Licenza

**GNU General Public License v3.0 o successiva**

## 🆘 Supporto

Per supporto, training o personalizzazioni:
- **Email**: c.comincini@invisiblefarm.com
- **Sito Web**: invisiblefarm.com

---

**📦 Versione**: v1.0.0  
**🎯 Compatibilità**: Moodle 3.9 - 4.x  
**📅 Ultimo Aggiornamento**: Novembre 2025  
**👨‍💻 Maintainer**: Carlo Comincini (c.comincini@invisiblefarm.com)
