#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# Le device

import paho.mqtt.client as mqtt
import json
import logging

print("Lancement ")

# Configuration
mqttServer = "mqtt.iut-blagnac.fr"

topic_subscribe_room = "AM107/by-room/+/data"
topic_subscribe_solar = "solaredge/blagnac/overview"

logging.basicConfig(level=logging.INFO)


# Callback de réception des messages
def get_data(mqttc, obj, msg):
    try:

       # print(msg.payload)

        # Deserialize the received message
        jsonMsg = json.loads(msg.payload)

        if msg.topic == topic_subscribe_solar:
            energy = jsonMsg['lifeTimeData']['energy']
            power = jsonMsg['currentPower']['power']
            print(f"Energie : {energy}")
            print(f"Power : {power}")
            print("--------------")

        else :
            # Extract temperature value
            temperature = jsonMsg[0]['temperature']
            room = jsonMsg[1]['room']
            print(f"Temperature: {temperature}")
            print(f"Room: {room}")
            print("--------------")

    except (json.JSONDecodeError, KeyError) as e:
        logging.error("Erreur dans les données reçues : %s", e)




# Connexion et souscription
mqttc = mqtt.Client()
mqttc.connect(mqttServer, port=1883, keepalive=60)
mqttc.on_message = get_data
mqttc.subscribe(topic_subscribe_room, qos=0)
mqttc.subscribe(topic_subscribe_solar, qos=0)
print("Connexion établie : ")
mqttc.loop_forever()
