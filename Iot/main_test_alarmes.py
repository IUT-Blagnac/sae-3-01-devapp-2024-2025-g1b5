import time
import paho.mqtt.client as mqtt
import json
import logging
import configparser
import os
import ast

# Charger la configuration depuis le fichier config.ini
config = configparser.ConfigParser()
config.read('config.ini')

# Configuration MQTT
mqttServer = config['mqtt']['server']
mqttPort = int(config['mqtt']['port'])
mqttKeepalive = int(config['mqtt']['keepalive'])

# Configuration des topics
topic_salles = config['topics']['sub_salles']
topic_panneaux = config['topics']['sub_panneaux']

# Récupérer la fréquence depuis la configuration (en secondes)
frequence = int(config['frequence']['frequence'])

# Initialiser les logs
logging.basicConfig(level=logging.INFO)

# Charger les données et les seuils
donneesAM = ast.literal_eval(config['data']['donneesSalles'])  # Convertir la chaîne en une liste
donneesEDGE = ast.literal_eval(config['data']['donneesSolar'])

seuil_max = ast.literal_eval(config['seuil']['seuil_max'])
seuil_min = ast.literal_eval(config['seuil']['seuil_min'])

# Variables globales pour stocker les données reçues
data_to_save = []

# Variables de gestion du temps pour la fréquence de mise à jour
last_update_time = time.time()

# Callback de réception des messages
# Callback de réception des messages
def get_data(mqttc, obj, msg):
    global data_to_save  # Pour modifier la variable globale

    try:
        # Désérialiser le message reçu
        jsonMsg = json.loads(msg.payload)

        # Si le message provient des panneaux solaires
        if msg.topic == topic_panneaux:
            room = 'solar'
            solar_data = {}
            for key in donneesEDGE:
                if key in jsonMsg:
                    solar_data[key] = jsonMsg[key]

            if solar_data:
                data_to_save.append(('solar', solar_data))  # Ajouter les données à la liste

        # Si le message provient des salles
        else:
            room = jsonMsg[1]['room']
            data = {}

            for i in range(len(donneesAM)):
                donnee = jsonMsg[0][donneesAM[i]]
                data[donneesAM[i]] = donnee

            # Vérification des alarmes pour les données des salles
            check_and_trigger_alarm(data, seuil_max, seuil_min, room)

            # Ajouter les données des salles à la liste
            data_to_save.append((room, data))  # Ajouter les données à la liste

    except (json.JSONDecodeError, KeyError) as e:
        logging.error("Erreur dans les données reçues : %s", e)


# Sauvegarder les données dans les fichiers appropriés
def save_data_to_file():
    global data_to_save  # Pour accéder à la variable globale contenant les données

    if data_to_save:
        for room, data in data_to_save:
            if room == 'solar':
                save_data_to_file_solar(room, data)  # Sauvegarder les données des panneaux solaires
            else:
                save_data_to_file_salles(room, data)  # Sauvegarder les données des salles

        # Vider la liste après la sauvegarde
        data_to_save = []

# Sauvegarder les données des salles
def save_data_to_file_salles(room, donnees):
    file_name = "salles.json"

    if os.path.exists(file_name):
        with open(file_name, "r") as file:
            data = json.load(file)
    else:
        data = {}

    if room not in data:
        data[room] = {}

    suivant = str(len(data[room]))
    data[room][suivant] = donnees  # Sauvegarder les données sous un nouvel index

    with open(file_name, "w") as file:
        json.dump(data, file, indent=1)

    print(f"Données des AM107 enregistrées dans le fichier {file_name}")

# Sauvegarder les données des panneaux solaires
def save_data_to_file_solar(room, solar_data):
    file_name = f"{room}.json"

    # Si le fichier existe déjà, on charge les données existantes
    if os.path.exists(file_name):
        with open(file_name, "r") as file:
            data = json.load(file)
    else:
        data = {}

    # Vérifier si data est une liste, et la convertir en dictionnaire si nécessaire
    if isinstance(data, list):
        data = {}

    # Si la salle 'solar' n'existe pas encore dans les données, on l'ajoute
    if room not in data:
        data[room] = {}

    # Ajouter les nouvelles données sous un index numéroté
    index = str(len(data[room]))  # Numéro de l'entrée (0, 1, 2, ...)
    data[room][index] = solar_data  # Ajout des données avec un index numéroté

    # Sauvegarder les données dans le fichier
    with open(file_name, "w") as file:
        json.dump(data, file, indent=2)

    print(f"Données des panneaux solaires enregistrées dans le fichier {file_name}")


def check_and_trigger_alarm(data, seuil_max, seuil_min, room):
    """
    Compare les données reçues aux seuils définis et déclenche une alarme si nécessaire.

    :param data: Dictionnaire des données reçues
    :param seuil_max: Liste des seuils maximums
    :param seuil_min: Liste des seuils minimums
    :param room: Nom de la salle ou source des données
    """
    for i, (key, value) in enumerate(data.items()):
        try:
            # Convertir la valeur en float pour éviter les erreurs de comparaison
            value = float(value)
        except ValueError:
            logging.error(f"Erreur de conversion de {key} dans {room} : valeur {value} invalide.")
            continue  # Passer à l'élément suivant si la conversion échoue

        # Récupérer les seuils max et min à l'index correspondant à la donnée
        max_threshold = seuil_max[i] if i < len(seuil_max) else float('inf')
        min_threshold = seuil_min[i] if i < len(seuil_min) else float('-inf')

        # Vérification du dépassement des seuils max et min
        if value > max_threshold:
            logging.warning(f"🚨 ALARME : {key} dans {room} dépasse le seuil MAX ({value} > {max_threshold})")
            trigger_alarm(room, key, value, "MAX")

        elif value < min_threshold:
            logging.warning(f"🚨 ALARME : {key} dans {room} dépasse le seuil MIN ({value} < {min_threshold})")
            trigger_alarm(room, key, value, "MIN")



def trigger_alarm(room, key, value, alarm_type):
    """
    Déclenche une alarme et la sauvegarde dans un fichier JSON.

    :param room: Nom de la salle ou source des données
    :param key: Clé de la donnée concernée
    :param value: Valeur de la donnée
    :param alarm_type: Type d'alarme ("MAX" ou "MIN")
    """
    timestamp = time.strftime("%Y-%m-%d %H:%M:%S")  # Horodatage
    logging.error(f"⚠️ ALARME DÉCLENCHÉE - {alarm_type} dans {room} pour {key}: {value} à {timestamp}")

    # Créer les données d'alarme
    alarm_data = {
        "key": key,
        "value": value,
        "alarm_type": alarm_type,
        "timestamp": timestamp
    }

    # Sauvegarder l'alarme dans le fichier
    save_alarm_to_file(room, alarm_data)


def save_alarm_to_file(room, alarm_data):
    """
    Sauvegarde une alarme dans un fichier JSON sous une structure organisée par salle.

    :param room: Nom de la salle ou source des données
    :param alarm_data: Dictionnaire contenant les détails de l'alarme
    """
    file_name = "alarmes.json"

    # Charger les données existantes si le fichier existe
    if os.path.exists(file_name):
        with open(file_name, "r") as file:
            data = json.load(file)
    else:
        data = {}

    # Si la salle n'est pas encore enregistrée, l'ajouter
    if room not in data:
        data[room] = {}

    # Ajouter l'alarme avec un index incrémental
    suivant = str(len(data[room]))
    data[room][suivant] = alarm_data

    # Sauvegarder dans le fichier
    with open(file_name, "w") as file:
        json.dump(data, file, indent=1)

    print(f"Alarme pour la salle {room} enregistrée dans le fichier {file_name}")








# Connexion et souscription MQTT
mqttc = mqtt.Client()
mqttc.connect(mqttServer, port=mqttPort, keepalive=mqttKeepalive)
mqttc.on_message = get_data
mqttc.subscribe(topic_salles, qos=0)
mqttc.subscribe(topic_panneaux, qos=0)

print("Connexion établie : ")
print("--------------")

# Boucle pour gérer la fréquence de lecture et la mise à jour des fichiers
while True:
    # Exécuter la boucle MQTT
    mqttc.loop(timeout=1.0)  # Timeout court pour éviter de bloquer trop longtemps

    # Si le temps écoulé dépasse la fréquence, sauvegarder les données
    current_time = time.time()
    if current_time - last_update_time >= frequence:
        # Mettre à jour les fichiers JSON
        save_data_to_file()

        last_update_time = current_time

    time.sleep(0.1)
