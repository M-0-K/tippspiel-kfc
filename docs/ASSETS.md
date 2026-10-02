# 🎨 KFC Tippspiel – Assets & Bild-Prompts

Diese Liste zeigt, welche Bilder die Webseite nutzt, welche schon da sind und welche du noch erzeugen musst, inklusive fertiger Prompts.

- **Originale** liegen in `assets/`. Sie werden nicht ausgeliefert (`assets/.htaccess`).
- **Fertige Bilder für die Webseite** kommen nach `data/…` und tragen **genau den Dateinamen aus der Tabelle**. Dann findet die Seite sie automatisch.
- Fehlt ein Bild, nutzt die Seite einen CSS-Ersatz (Verlauf/Platzhalter). Die Seite ist also nie kaputt, du kannst die Bilder nach und nach ergänzen.

---

## ✅ Checkliste (zum Abhaken)

**Wichtig (zuerst):**
- [ ] #3 Kämpferbilder: ein Bild pro Kämpfer → `data/kaempfer/<name>.jpg`, danach in der DB eintragen
- [x] #8 Teamwappen → `data/logo/rote_funken.png`, `data/logo/lange_garde.png` (automatisch erzeugt, Garde optional sauberer freistellen)
- [ ] Einverständnis der Kämpfer für ihre Bilder (auch KI-bearbeitet) auf Seite und Beamer

**Schön zu haben:**
- [ ] #1 `data/bg/halle_desktop.jpg`
- [ ] #2 `data/bg/clash_quer.jpg`
- [ ] #5 Face-off Hauptkampf `data/kaempfer/faceoff_7.jpg`

**Kür:**
- [ ] #4 freigestellte Kämpfer `data/kaempfer/<name>_frei.png`
- [ ] #6 Platzhalter-Silhouetten `data/kaempfer/none_rot.jpg`, `none_gelb.jpg` (es gibt schon einfache Platzhalter)
- [ ] #9 `data/sieger_banner.png`
- [ ] #10 `data/ko_splash.png`
- [ ] #11 `data/champion_guertel.png`

---

## 📁 Vorhandene Assets und was damit passiert

| Datei in `assets/` | Was drauf ist | Verwendung auf der Seite | Status |
|---|---|---|---|
| `KFC Logo.png` | Logo: Krone, ★★★, KFC in Gold, KULOW FIGHTERS, CHAMPIONSHIP auf Schwarz | Header, Startseite, Liveansicht, Favicon (Krone) | wird automatisch zugeschnitten → `data/logo/kfc_logo.png` |
| `image.png` | KFC-Plakat 10.10 | Link-Vorschau in WhatsApp (`og:image`) | → `data/share.jpg` |
| `WhatsApp … 17.17.15.jpeg` | Echte Halle: leerer Ring, Flutlicht, RED/YELLOW CORNER | Seitenhintergrund (Handy) | → `data/bg/halle_mobile.jpg` |
| `WhatsApp … 17.19.13.jpeg` | Handschuh-Clash rot vs. gelb | Startseite-Hero, Teamstand, Liveansicht-Pause | → `data/bg/clash_mobile.jpg` |
| `WhatsApp … 17.17.15 (2).jpeg` | Kämpfer in rot-schwarzer Hose, rote Ecke | **Stilvorlage** für Funken-Kämpferbilder | Beispielbild → `data/kaempfer/beispiel_rot.jpg` |
| `WhatsApp … 17.17.15 (1).jpeg` | Kämpfer in gelber Hose, Guard-Pose | **Stilvorlage** für Garde-Kämpferbilder | Beispielbild → `data/kaempfer/beispiel_gelb.jpg` |
| `WhatsApp … 17.17.15 (3).jpeg` | Face-off (rot-weiß vs. gelb), "THAILAND STADIUM" | Nur **Kompositionsvorlage** für #5 (fremdes Branding) | nicht direkt verwendet |
| `WhatsApp … 18.0.jpeg` | Lange-Garde-Wappen mit Fahnen, auf Schwarz | Teamwappen Garde | ✅ automatisch freigestellt → `data/logo/lange_garde.png` (bei Bedarf sauberer mit remove.bg, #8) |
| `WhatsApp … 18.31.jpeg` | Gleiches Wappen auf Weiß | Reserve | – |
| `WhatsApp … 18.49.31.jpeg` | Einfaches ovales Garde-Logo | kleines Icon (gut lesbar bei 24–32 px) | optional freistellen → `data/logo/lange_garde_klein.png` |
| `FunkenLogo (1).pdf` | Rote-Funken-Wappen (Vektor) | Teamwappen Funken | ✅ automatisch aus dem PDF exportiert → `data/logo/rote_funken.png` |
| `WhatsApp Video … 17.17.53.mp4` | noch nicht gesichtet | evtl. stummes Loop-Video für die Liveansicht | auf ca. 10 s / < 3 MB kürzen → `data/bg/loop.mp4` |

---

## 🧠 Grundregeln für alle Prompts

Die Prompts funktionieren mit **ChatGPT (Bild)**, **Gemini**, **Midjourney** (`--sref` mit Referenzbild) oder **Adobe Firefly**. Lade jeweils die genannten **Referenzbilder** mit hoch.

1. **Keinen Text ins Bild.** Namen, "VS", "SIEGER", "K.O." schreibt die Webseite selbst. KI-Text wird krumm.
2. **Echte Gesichter beibehalten:** immer das Foto der Person hochladen und *"keep the face and identity exactly the same"* schreiben.
3. **Werbung entfernen:** "EVERLAST", "THAILAND" usw. wegretuschieren lassen.
4. **Gleicher Bildausschnitt** bei allen Kämpferbildern, sonst wirken die Karten unruhig.

**Stil-Suffix** – an jeden Prompt anhängen, damit alles zusammenpasst:

```
same lighting and atmosphere as the reference: dark sports hall, truss rigging with bright white floodlights,
haze, red and white ring ropes with yellow accents, dark maroon ring canvas, red corner vs. yellow corner,
cinematic high contrast, subtle gold accents, photorealistic, no text, no letters, no logos, no watermarks
```

---

## 🖼️ Assets zum Erzeugen

### #1 Hallen-Hintergrund Desktop
- **Datei:** `data/bg/halle_desktop.jpg` · **Format:** 1920×1080 JPG, < 300 KB
- **Wofür:** Seitenhintergrund am PC/Laptop (am Handy wird `halle_mobile.jpg` genutzt)
- **Referenz:** `assets/WhatsApp Image 2026-10-02 at 17.17.15.jpeg`

```
Extend this photo of the boxing hall to a wide 16:9 landscape format (outpainting).
Keep the ring, the truss with floodlights and the audience. Make the ring empty and centered,
slightly darker overall, remove all brand names from the corner pads (keep them plain red and plain yellow).
No text.
```

### #2 Handschuh-Clash quer
- **Datei:** `data/bg/clash_quer.jpg` · **Format:** 1920×1080 JPG, < 300 KB
- **Wofür:** Startseite-Hero am Desktop, Hintergrund der Teamstand-Leiste
- **Referenz:** `assets/WhatsApp Image 2026-10-02 at 17.19.13.jpeg`

```
Extend this image to a wide 16:9 landscape format. Keep the red glove on the left and the yellow glove
on the right colliding in the center with the burst of sparks and debris, red smoke and spotlights on the
left half, yellow smoke and spotlights on the right half, ring ropes at the bottom.
Leave dark space at the top center for a logo. No text.
```

### #3 Kämpferbild (eins pro Kämpfer) ⭐ wichtigstes Asset
- **Datei:** `data/kaempfer/<vorname>.jpg` (klein geschrieben, ohne Umlaute/Leerzeichen, z.B. `max.jpg`)
- **Format:** 900×1200 (3:4) JPG, < 150 KB
- **Wofür:** Kampfkarten beim Tippen, Kampfabend, Liveansicht
- **Referenz:** Foto des Kämpfers + Stilvorlage `17.17.15 (2).jpeg` (Funken) bzw. `17.17.15 (1).jpeg` (Garde)
- **Ausschnitt:** Kopf im oberen Drittel, Fäuste oben, Hüfte am unteren Rand, bei allen gleich.

**Rote Funken (rote Ecke):**
```
Use the person from the first photo and keep the face and identity exactly the same.
Create a promo portrait in the exact style of the second image: shirtless fighter standing in the RED corner
of the boxing ring, fists raised in guard, red and black Thai boxing shorts with gold ornaments,
dark sports hall with floodlights and audience in the background, waist-up, portrait 3:4.
No text, no brand names on the corner pad.
```

**Lange Garde (gelbe Ecke):**
```
Use the person from the first photo and keep the face and identity exactly the same.
Create a promo portrait in the exact style of the second image: shirtless fighter standing in the YELLOW corner
of the boxing ring, fists raised in guard, yellow Thai boxing shorts with gold ornaments,
dark sports hall with floodlights and audience in the background, waist-up, portrait 3:4.
No text, no brand names on the corner pad.
```

**Danach in der DB eintragen** (phpMyAdmin → SQL):
```sql
UPDATE kaempfer SET Name = 'Max Mustermann', Spitzname = 'Der Hammer', Bild = 'max.jpg' WHERE Kpid = 1;
```

### #4 Kämpfer freigestellt (optional)
- **Datei:** `data/kaempfer/<vorname>_frei.png` · **Format:** 900×1200 PNG transparent
- **Wofür:** große Darstellung in der Liveansicht mit Teamfarben-Glow. Wenn vorhanden, nutzt die Seite es automatisch, sonst das normale Bild.
- **Vorgehen:** Bild aus #3 mit remove.bg, Canva oder Photoshop freistellen, oder per Prompt:

```
Remove the background completely, keep only the fighter including hair and shorts, transparent PNG.
```

### #5 Face-off (Hauptkampf)
- **Datei:** `data/kaempfer/faceoff_<reihenfolge>.jpg` (Hauptkampf = `faceoff_7.jpg`)
- **Format:** 1920×1080 JPG, < 350 KB
- **Wofür:** Hintergrund der Hauptkampf-Karte, Liveansicht vor dem Kampf, WhatsApp-Teaser
- **Referenz:** Foto Kämpfer 1 (Funken) + Foto Kämpfer 2 (Garde) + `17.17.15 (3).jpeg` (Komposition) + `17.17.15.jpeg` (Halle)

```
Face-off of two fighters, composition like the third reference: both standing close, forehead to forehead,
fists raised. Left: person from photo 1 (keep face exactly) in red and black Thai shorts with gold ornaments,
red smoke and red spotlights behind him. Right: person from photo 2 (keep face exactly) in yellow Thai shorts
with gold ornaments, yellow smoke and yellow spotlights behind him. Setting: the boxing hall from the fourth
reference, truss with floodlights, audience in the dark. No punching bag, no ring logo, no 'Thailand', no text.
Cinematic, dramatic, poster style, 16:9.
```

### #6 Platzhalter-Silhouetten (optional, einfache Version ist schon da)
- **Dateien:** `data/kaempfer/none_rot.jpg`, `data/kaempfer/none_gelb.jpg` · **Format:** 900×1200 PNG
- **Wofür:** solange ein Kämpferbild fehlt
- **Referenz:** `17.17.15 (2).jpeg`

```
Dark silhouette of a shirtless fighter in guard position, waist-up, same pose and framing as the reference,
no facial features, strong red rim light from behind, black background, no text
```
(für Gelb: *"strong yellow rim light"*)

### #7 Goldtextur
- **Datei:** `data/gold_texture.jpg`. **Wird automatisch aus dem KFC-Logo erzeugt**, nichts zu tun.

### #8 Teamwappen ⭐
- **Dateien:** `data/logo/rote_funken.png`, `data/logo/lange_garde.png` · **Format:** ca. 800 px hoch, PNG transparent
- **Kein KI-Bild:** Vereinswappen bitte im Original lassen.
- **Rote Funken:** `assets/FunkenLogo (1).pdf` als PNG exportieren, z.B. mit Inkscape (Datei → Exportieren → PNG, Hintergrund transparent), [cloudconvert.com](https://cloudconvert.com/pdf-to-png) oder Adobe Acrobat. Den weißen Rand wegschneiden.
- **Lange Garde:** `assets/WhatsApp Image 2026-09-30 at 18.0.jpeg` mit [remove.bg](https://www.remove.bg) freistellen (schwarzer Hintergrund → transparent).

### #9 Sieger-Banderole (optional)
- **Datei:** `data/sieger_banner.png` · **Format:** 1600×300 PNG transparent
- **Wofür:** "SIEGER" über dem Gewinner in der Liveansicht (der Text kommt von der Seite)
- **Referenz:** `assets/KFC Logo.png`

```
Horizontal ribbon banner in cracked gold leaf exactly like the gold of the reference logo, small crown in the
top center like the reference crown, three small stars on each side, blank ribbon, transparent background, no text
```

### #10 K.O.-Splash (optional)
- **Datei:** `data/ko_splash.png` · **Format:** 1000×1000 PNG (schwarzer Hintergrund reicht)
- **Wofür:** Effekt bei K.O. in der Liveansicht (per `mix-blend-mode: screen`, Schwarz wird unsichtbar)
- **Referenz:** `assets/WhatsApp Image 2026-10-02 at 17.19.13.jpeg`

```
Only the impact burst from the center of the reference: white-hot flash, sparks and flying debris radiating
outward, no gloves, on pure black background, no text
```

### #11 Champion-Gürtel (optional)
- **Datei:** `data/champion_guertel.png` · **Format:** 1000×500 PNG transparent
- **Wofür:** Platz 1 im Ranking
- **Referenz:** `assets/KFC Logo.png`

```
Championship belt, black leather strap, large center plate in cracked gold leaf like the reference,
crown and stars on the plate like the reference logo, left side plate red, right side plate yellow,
front view, studio light, transparent background, no text
```

### #12 Icons
Nichts zu tun: Die Icons (Handschuh, Krone, Glocke, Pokal, Stoppuhr, Live, Stern) sind als einfache SVGs direkt im Code enthalten.

---

## 🔧 Bilder verkleinern (falls zu groß)

Ziel: Kämpferbilder < 150 KB, Hintergründe < 300 KB. Am einfachsten mit [squoosh.app](https://squoosh.app): Bild hineinziehen, MozJPEG, Qualität 75–80, Breite wie oben angegeben.
