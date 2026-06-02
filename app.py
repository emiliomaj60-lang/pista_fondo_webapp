from flask import Flask, render_template, request, redirect, url_for, session
import json
import os

app = Flask(__name__)

app.secret_key = "whhht6789skdhy367s22S6"  # Cambiala con una stringa lunga e sicura
ADMIN_PASSWORD = "pista2024"

# Percorso file prenotazioni
PRENOTAZIONI_FILE = "prenotazioni.json"


# -----------------------------
# FUNZIONI DI UTILITÀ
# -----------------------------

def salva_prenotazione(dati):
    if not os.path.exists(PRENOTAZIONI_FILE):
        with open(PRENOTAZIONI_FILE, "w") as f:
            json.dump([], f)

    with open(PRENOTAZIONI_FILE, "r") as f:
        lista = json.load(f)

    lista.append(dati)

    with open(PRENOTAZIONI_FILE, "w") as f:
        json.dump(lista, f, indent=4)


def leggi_stato_pista():
    try:
        with open("stato_pista.json", "r") as f:
            return json.load(f)
    except:
        return {
            "stato": "chiusa",
            "km": 0,
            "messaggio": "Dati non disponibili"
        }


def leggi_maestri():
    with open("maestri.json", "r") as f:
        return json.load(f)


def salva_maestri(lista):
    with open("maestri.json", "w") as f:
        json.dump(lista, f, indent=4)


# -----------------------------
# ROTTE PUBBLICHE
# -----------------------------

@app.route("/")
def index():
    stato = leggi_stato_pista()
    return render_template("index.html", stato=stato)


@app.route("/pista")
def pista():
    return render_template("pista.html")


@app.route("/scuola-sci")
def scuola_sci():
    return render_template("scuola_sci.html")


@app.route("/prenotazioni", methods=["GET", "POST"])
def prenotazioni():
    if request.method == "POST":
        dati = {
            "nome": request.form.get("nome"),
            "email": request.form.get("email"),
            "telefono": request.form.get("telefono"),
            "data": request.form.get("data"),
            "orario": request.form.get("orario"),
            "livello": request.form.get("livello"),
            "note": request.form.get("note")
        }

        salva_prenotazione(dati)
        return redirect(url_for("conferma"))

    return render_template("prenotazioni.html")


@app.route("/conferma")
def conferma():
    return render_template("conferma.html")


# -----------------------------
# LOGIN / LOGOUT
# -----------------------------

@app.route("/admin/login", methods=["GET", "POST"])
def admin_login():
    if request.method == "POST":
        password = request.form.get("password")
        if password == ADMIN_PASSWORD:
            session["logged_in"] = True
            return redirect(url_for("admin_dashboard"))
        else:
            return render_template("admin_login.html", errore=True)

    return render_template("admin_login.html", errore=False)


@app.route("/admin/logout")
def admin_logout():
    session.clear()
    return redirect(url_for("index"))


# -----------------------------
# ROTTE ADMIN (protette)
# -----------------------------

@app.route("/admin/dashboard")
def admin_dashboard():
    if not session.get("logged_in"):
        return redirect(url_for("admin_login"))
    return render_template("admin_dashboard.html")


@app.route("/admin/stato-pista", methods=["GET", "POST"])
def admin_stato_pista():
    if not session.get("logged_in"):
        return redirect(url_for("admin_login"))

    stato = leggi_stato_pista()

    if request.method == "POST":
        nuovo_stato = {
            "stato": request.form.get("stato"),
            "km": int(request.form.get("km")),
            "messaggio": request.form.get("messaggio")
        }

        with open("stato_pista.json", "w") as f:
            json.dump(nuovo_stato, f, indent=4)

        return redirect(url_for("admin_stato_pista"))

    return render_template("admin_stato_pista.html", stato=stato)


@app.route("/admin/maestri")
def admin_maestri():
    if not session.get("logged_in"):
        return redirect(url_for("admin_login"))

    maestri = leggi_maestri()
    return render_template("admin_maestri.html", maestri=maestri)


@app.route("/admin/prenotazioni")
def admin_prenotazioni():
    if not session.get("logged_in"):
        return redirect(url_for("admin_login"))

    if not os.path.exists("prenotazioni.json"):
        prenotazioni = []
    else:
        with open("prenotazioni.json", "r") as f:
            prenotazioni = json.load(f)

    return render_template("admin_prenotazioni.html", prenotazioni=prenotazioni)


@app.route("/admin/corsi")
def admin_corsi():
    if not session.get("logged_in"):
        return redirect(url_for("admin_login"))

    with open("corsi.json", "r") as f:
        corsi = json.load(f)

    return render_template("admin_corsi.html", corsi=corsi)


# -----------------------------
# AVVIO APP
# -----------------------------

if __name__ == "__main__":
    app.run(debug=True)