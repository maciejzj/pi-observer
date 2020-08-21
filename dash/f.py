'''

Adapted excerpt from Getting Started with Raspberry Pi by Matt Richardson

Modified by Rui Santos
Complete project details: https://randomnerdtutorials.com

'''

import RPi.GPIO as GPIO
from flask import Flask, render_template, request, flash, redirect, url_for
from config import Config
from forms import LoginForm


app = Flask(__name__)
app.config.from_object(Config)

GPIO.setmode(GPIO.BCM)

pins = {
   22 : {'name' : 'Buzzer', 'state' : GPIO.LOW},
   27 : {'name' : 'led', 'state' : GPIO.LOW}
}

for pin in pins:
   GPIO.setup(pin, GPIO.OUT)
   GPIO.output(pin, GPIO.LOW)


@app.route('/login', methods=['GET', 'POST'])
def login():
    form = LoginForm()
    if form.validate_on_submit():
        flash('Login requested for user {}, remember_me={}'.format(
            form.username.data, form.remember_me.data))
        return redirect(url_for('main'))
    return render_template('login.html', title='Sign In', form=form)


@app.route("/")
def main():
   for pin in pins:
      pins[pin]['state'] = GPIO.input(pin)

   template_data = {
      'pins' : pins
   }
   return render_template('index.html', **template_data)


@app.route("/<change_pin>/<action>")
def action(change_pin, action):
   change_pin = int(change_pin)

   if action == "on":
      GPIO.output(change_pin, GPIO.HIGH)
   if action == "off":
      GPIO.output(change_pin, GPIO.LOW)

   for pin in pins:
      pins[pin]['state'] = GPIO.input(pin)

   template_data = {
      'pins' : pins
   }
   return render_template('index.html', **template_data)

if __name__ == "__main__":
   app.run(host='0.0.0.0', port=5000, debug=True)
