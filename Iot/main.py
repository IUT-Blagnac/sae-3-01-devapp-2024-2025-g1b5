import time
import paho.mqtt.client as mqtt
import json
import logging
import configparser
import os
import ast

print("Lancement ")

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

logging.basicConfig(level=logging.INFO)

donneesAM = ast.literal_eval(config['data']['donneesSalles'])  # Convertir la chaîne en une liste
donneesEDGE = ast.literal_eval(config['data']['donneesSolar'])

seuil_max = ast.literal_eval(config['seuil']['seuil_max'])
seuil_min = ast.literal_eval(config['seuil']['seuil_min'])


# Callback de réception des messages
def get_data(mqttc, obj, msg):
    try:
        # Désérialiser le message reçu
        jsonMsg = json.loads(msg.payload)

        # Si le message provient des panneaux solaires
        if msg.topic == topic_panneaux:
            room = 'solar'
            # Filtrer les données à afficher et enregistrer en fonction de 'donneesSolar'
            solar_data = {}
            for key in donneesEDGE:  # donneesEDGE doit contenir les clés comme 'lastUpdateTime', 'lifeTimeData', etc.
                # Vérifier si la clé existe dans le message MQTT et l'ajouter à 'solar_data'
                if key in jsonMsg:
                    solar_data[key] = jsonMsg[key]
                else:
                    print(f"Avertissement: La clé '{key}' n'a pas été trouvée dans le message MQTT.")

            # Si des données ont été extraites, les afficher
            if solar_data:
                print("\nDernières infos des panneaux solaires :")
                for key, value in solar_data.items():
                    print(f"{key}: {value}")

                # Sauvegarder les données dans le fichier JSON
                save_data_to_file_solar(room, solar_data)
            else:
                print("Aucune donnée solaire valide trouvée.")

        # Si le message provient des salles
        else:
            room = jsonMsg[1]['room']
            print(f"\nRoom: {room}")
            data = {}

            for i in range(len(donneesAM)):
                donnee = jsonMsg[0][donneesAM[i]]
                data[donneesAM[i]] = donnee

            print(data)
            save_data_to_file(room, data)

    except (json.JSONDecodeError, KeyError) as e:
        logging.error("Erreur dans les données reçues : %s", e)


def save_data_to_file(room, donnees: dict):
    file_name = "salles.json"

    if os.path.exists(file_name):
        with open(file_name, "r") as file:
            data = json.load(file)
    else:
        data = {}

    if room not in data:
        data[room] = {}

    suivant = str(len(data[room]))
    data[room][suivant] = donneesAM

    with open(file_name, "w") as file:
        json.dump(data, file, indent=1)


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




# Connexion et souscription
mqttc = mqtt.Client()
mqttc.connect(mqttServer, port=mqttPort, keepalive=mqttKeepalive)
mqttc.on_message = get_data
mqttc.subscribe(topic_salles, qos=0)
mqttc.subscribe(topic_panneaux, qos=0)

print("Connexion établie : ")
print("--------------")

# Boucle pour gérer la fréquence de lecture
while True:
    # Exécuter la boucle MQTT
    mqttc.loop(timeout=1.0)  # Timeout court pour éviter de bloquer trop longtemps

    # Attendre la prochaine itération selon la fréquence spécifiée
    time.sleep(frequence)  # Attendre selon la fréquence définie dans config.ini
