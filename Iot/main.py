import ast

import paho.mqtt.client as mqtt
import json
import logging
import configparser
import os

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

logging.basicConfig(level=logging.INFO)

donnees = ast.literal_eval(config['data']['donnees'])  # Convertir la chaîne en une liste

seuil_max = ast.literal_eval(config['seuil']['seuil_max'])
seuil_min = ast.literal_eval(config['seuil']['seuil_min'])


# Callback de réception des messages
def get_data(mqttc, obj, msg):
    try:
        # print(msg.payload)

        # Désérialiser le message reçu
        jsonMsg = json.loads(msg.payload)

        if msg.topic == topic_panneaux:

            room = 'solar'
            energy = jsonMsg['lifeTimeData']['energy']
            power = jsonMsg['currentPower']['power']
            print("\nDernières infos des panneaux solaires :")
            print(f"Energie : {energy}")
            print(f"Power : {power}")
            save_data_to_file_solar(room, energy, power)



        else:
            room = jsonMsg[1]['room']
            print(f"\nRoom: {room}")
            data = {}

            for i in range(len(donnees)):
                donnee = jsonMsg[0][donnees[i]]
                data[donnees[i]] = donnee

            print(data)
            save_data_to_file( room, data)




    except (json.JSONDecodeError, KeyError) as e:
        logging.error("Erreur dans les données reçues : %s", e)


def save_data_to_file(room, donnees: dict):

    file_name = "salles.json"

    if os.path.exists(file_name):
        with open(file_name, "r") as file:
            data = json.load(file)
    else :
        data = {}
    
    
    if room not in data:
        data[room] = {}

    suivant = str(len(data[room]))
    data[room][suivant] = donnees

    
    with open(file_name, "w") as file:
        json.dump(data, file, indent=1)

def save_data_to_file_solar(room, energy, power):
    file_name = f"{room}.json"

    if os.path.exists(file_name):
        with open(file_name, "r") as file:
            data = json.load(file)       
    else:
        data = []

    # Ajouter les nouvelles données sous forme d'un dictionnaire
    new_data = {}
    new_data["energy"] = energy
    new_data["power"] = power
    data.append(new_data)

    # Sauvegarder les données dans le fichier
    with open(file_name, "w") as file:
        json.dump(data, file, indent=2)

    print(f"Données de la salle {room} enregistrées dans le fichier {file_name}")


# Connexion et souscription
mqttc = mqtt.Client()
mqttc.connect(mqttServer, port=mqttPort, keepalive=mqttKeepalive)
mqttc.on_message = get_data
mqttc.subscribe(topic_salles, qos=0)
mqttc.subscribe(topic_panneaux, qos=0)
print("Connexion établie : ")
print("--------------")
mqttc.loop_forever()