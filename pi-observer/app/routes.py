from flask import Flask, flash, render_template, request, redirect, url_for
from flask_login import current_user, login_required, login_user, logout_user
from werkzeug.urls import url_parse
import RPi.GPIO as GPIO

from app import app, db, dash_app
from app.forms import LoginForm, RegistrationForm
from app.models import User


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
    if current_user.is_authenticated:
        return redirect(url_for('index'))
    form = LoginForm()
    if form.validate_on_submit():
        user = User.query.filter_by(username=form.username.data).first()
        if user is None or not user.check_password(form.password.data):
            flash('Invalid username or password')
            return redirect(url_for('login'))
        login_user(user, remember=form.remember_me.data)
        next_page = request.args.get('next')
        if not next_page or url_parse(next_page).netloc != '':
            next_page = url_for('index')
        return redirect(next_page)
    return render_template('login.html', title='Sign In', form=form)


@app.route('/logout')
def logout():
    logout_user()
    return redirect(url_for('index'))


@app.route("/")
@app.route("/index")
@login_required
def index():
   for pin in pins:
      pins[pin]['state'] = GPIO.input(pin)

   template_data = {
      'pins' : pins
   }
   return render_template('index.html', **template_data)


@app.route('/register', methods=['GET', 'POST'])
def register():
    if current_user.is_authenticated:
        return redirect(url_for('index'))
    form = RegistrationForm()
    if form.validate_on_submit():
        user = User(username=form.username.data, email=form.email.data)
        user.set_password(form.password.data)
        db.session.add(user)
        db.session.commit()
        flash('Congratulations, you are now a registered user!')
        return redirect(url_for('login'))
    return render_template('register.html', title='Register', form=form)


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


@login_required
@app.route('/dash')
def my_dash_app():
    return dash_app.index()
