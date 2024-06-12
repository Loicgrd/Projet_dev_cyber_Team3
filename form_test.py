import requests
from selenium import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from bs4 import BeautifulSoup
import time
import datetime as dt
import csv

def find_form(url):
    """Fonction permettant d'enregistrer le formulaire en executant dans un navigateur"""
    driver = webdriver.Chrome() #Ouvre navigateur Chrome (pour pourvoir executer les .js)
    driver.get(url) #Permet d'ouvrir la page de connexion
    wait = WebDriverWait(driver, 10) # Attendre que les éléments du formulaire soient présents
    html_out = driver.page_source # Extraire le contenu de la page html retour
    soup = BeautifulSoup(html_out, 'html.parser')# Analyser le contenu HTML
    form = soup.find('form') # Extraire les élement entre les balises form
    return str(form)


def find_user_pswd_button(url):
    """Trouve le nom des entrées du formulaire et l'id du boutton"""
    name_data = 'data-rel' #Nom du type de variable pour le login et password
    form = find_form(url)
    soup = BeautifulSoup(form, 'html.parser')
    form = soup.find('form')
    user = form.find('input', {'type': 'text'}) #Cherche les entrées de type text
    user_name = user[name_data]
    pswd = form.find('input', {'type': 'password'}) #Cherche les entrées de type password
    pswd_name = pswd[name_data]
    button = form.find('button', {'type' : 'button'}) #Cherche le boutton d'envoie du formulaire
    button_id = button['id']
    return str(user_name), str(pswd_name), str(button_id)


'''======Données spécifique formulaire======'''
url = 'http://localhost:5173/' #URL du formulaire
url_back = 'http://localhost:8003/signin' #URL du back
error_message = "No user were found with this credentials, using password" #Message de réponse d'erreur du formulaire

"""Liste d'exploits"""
exploit_name = ["Correct login/password", "False login/password", 
                "SQL Injection Login (' OR '1'='1'; --)", "SQL Injection Login (or 1-- -' or 1 or '1'or 1 or')",
                "SQL Injection Password (' OR '1'='1'; --)", "SQL Injection Password (or 1-- -' or 1 or '1'or 1 or')",
                "XSS"] #Liste nom exploit
login_list = ["admin", "admin" ,
                "' OR '1'='1'; --", "or 1-- -' or 1 or '1'or 1 or'",
                "false_login", "false_login",
                "<Script>alert(“hack by falcon”)</Script>"] #Liste Login
password_list = ["super_admin", "false_password",
                  "false_password", "false_paswd",
                  "' OR '1'='1'; --", "or 1-- -' or 1 or '1'or 1 or'",
                  "<Script>alert(“hack by falcon”)</Script>"] #Liste Password
'''======================================'''



#Initialisation des payloads
user_name, pswd_name, button_id = find_user_pswd_button(url)

#Incrémentation du dictionnaire des exploits à tester
payloads={}
for i in range (len(login_list)):
    payloads[i] = {user_name : login_list[i],
                pswd_name : password_list[i],
                }

#Ouverture du fichier d'écriture
fileName="Exploits_rapport2.csv" #+str(dt.datetime.now().hour)+str(dt.datetime.now().minute)+str(dt.datetime.now().second)
file = open("./"+fileName, "w") 
writer = csv.writer(file, delimiter=':')
writer.writerow(["Exploit name","Access"])

#Boucle test d'exploits
for i in range (len(exploit_name)):
    # Envoyer la requête POST
    response = requests.post(url_back, data=payloads[i])
    # Récupération de la réponse
    response_text = response.text
    # Test sur la réponse
    if response.status_code == 200:
        print(f"Requete bien effectué pour : {exploit_name[i]}")
        if error_message in response.text:
            resultat = "Access Denied"
            print(resultat+ "\n")
        else:
            resultat = "Access accepted"
            print(resultat + "\n")
    else:
        resultat = f"Problème d'envoie requête à {url_back} pour tester {exploit_name[i]}"
        print(resultat +"\n")

    writer.writerow([exploit_name[i],resultat]) #Ecriture fichier csv
    #writer.writerows([[f"Login :{login_list[i]}",f"Password :{password_list[i]}"], [exploit_name[i],resultat]])
file.close()