import json
import logging
import configparser
import paho.mqtt.client as mqtt
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

# Structure pour stocker les données récupérées
data = {
    'panneaux': [],
    'salles': []
}

# Callback de réception des messages
def get_data(mqttc, obj, msg):
    try:
        # Désérialiser le message reçu
        jsonMsg = json.loads(msg.payload)

        if msg.topic == topic_panneaux:
            energy = jsonMsg['lifeTimeData']['energy']
            power = jsonMsg['currentPower']['power']
            data['panneaux'].append([energy, power])
            print("Dernières infos des panneaux solaires :")
            print(f"Energie : {energy}")
            print(f"Power : {power}\n")

        else:
            temperature = jsonMsg[0]['temperature']
            room = jsonMsg[1]['room']
            data['salles'].append([room, temperature])
            print(f"Room: {room}")
            print(f"Temperature: {temperature}\n")

        # Écriture des données dans un fichier texte après mise à jour
        save_data_to_file()

    except (json.JSONDecodeError, KeyError) as e:
        logging.error("Erreur dans les données reçues : %s", e)

# Fonction pour sauvegarder les données formatées dans un fichier texte
def save_data_to_file():
    output = ""
    for key, values in data.items():
        formatted_key = 'B202' if key == 'panneaux' else 'B201'
        formatted_values = ''.join([f"[{','.join(map(str, item))}]" for item in values])
        output += f"{formatted_key}{formatted_values}"
    
    with open("output.txt", "w") as file:
        file.write(output)

    print("Données enregistrées dans le fichier output.txt")

# Connexion et souscription
mqttc = mqtt.Client()
mqttc.connect(mqttServer, port=mqttPort, keepalive=mqttKeepalive)
mqttc.on_message = get_data
mqttc.subscribe(topic_salles, qos=0)
mqttc.subscribe(topic_panneaux, qos=0)
print("Connexion établie : ")
print("--------------")
mqttc.loop_forever()
