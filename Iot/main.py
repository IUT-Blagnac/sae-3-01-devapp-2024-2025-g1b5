
import paho.mqtt.client as mqtt
import json
import logging
import configparser

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

# Callback de réception des messages
def get_data(mqttc, obj, msg):
    try:
        # print(msg.payload)

        # Désérialiser le message reçu
        jsonMsg = json.loads(msg.payload)

        if msg.topic == topic_panneaux:
            energy = jsonMsg['lifeTimeData']['energy']
            power = jsonMsg['currentPower']['power']
            print("Dernières infos des panneaux solaires :")
            print(f"Energie : {energy}")
            print(f"Power : {power}\n")

        else:
            # Extraire la température
            temperature = jsonMsg[0]['temperature']
            room = jsonMsg[1]['room']
            print(f"Room: {room}")
            print(f"Temperature: {temperature}\n")

    except (json.JSONDecodeError, KeyError) as e:
        logging.error("Erreur dans les données reçues : %s", e)

# Connexion et souscription
mqttc = mqtt.Client()
mqttc.connect(mqttServer, port=mqttPort, keepalive=mqttKeepalive)
mqttc.on_message = get_data
mqttc.subscribe(topic_salles, qos=0)
mqttc.subscribe(topic_panneaux, qos=0)
print("Connexion établie : ")
print("--------------")
mqttc.loop_forever()
