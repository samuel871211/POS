# coding=utf-8
from flask import Flask, render_template, url_for, redirect, session, request
from werkzeug.security import check_password_hash, generate_password_hash
from werkzeug.exceptions import HTTPException
from flask_socketio import SocketIO, emit
from email.message import EmailMessage
from datetime import datetime
import smtplib
import mariadb
import random
import json
import time
import sys
import os

app = Flask(__name__)
app.config["SECRET_KEY"] = os.urandom(24)
socketio = SocketIO(app)

def connect_mariadb():
	global connection
	global cur
	try:
		connection = mariadb.connect(
			user = "root",
			password = "cat2163472",
			host = "localhost",
			port = 3306
		)
		cur = connection.cursor()
		return "ok"
	except mariadb.Error as e:
		return "no"

connect_mariadb()

@app.errorhandler(404)
def page_not_found(error):
	if request.method == "GET":
		return redirect(url_for('login'))
	else:
		return "app_not_found"
	'''
	1. Redirect to login.html if users request a wrong app route
	2. Return text if users pull a post request to wrong app route 
	'''

@app.route("/login.html")
def login():
	return render_template('login.html')

@app.route("/signIn",methods = ["POST"])
def signIn():
	branch = request.values.get("branch")
	username = request.values.get("username")
	password = request.values.get("password")
	try:
		sql = "SELECT * FROM admins.alladmins WHERE branch='"+branch+"' AND username='"+username+"' AND password='"+password+"'"
		cur.execute(sql)
		if cur.fetchall() == []:
			return "NO"
		session["branch"] = branch
		session["username"] = username
		session["password"] = password
		session["usernameHash"] = generate_password_hash(username)
		return "OK"
	except:
		status = connect_mariadb()
		if status == "ok":
			return signIn()
		else:
			return status

@app.route('/counter.html')
def counter():
	# if "branch" not in session.keys():
	# 	return redirect(url_for("login"))
	# return render_template('counter.html',usernameHash = session["usernameHash"])
	return render_template('counter.html')

@socketio.on('FindItemByBarcode')
def FindItemByBarcode(barcode):
	if not barcode.isdigit():
		socketio.emit('FindItemByBarcode_Result'+session["usernameHash"],'Invalid barcode')
	else:	
		target = 'ean13' if len(barcode) == 13 else 'number'
		prefix = barcode[7] if len(barcode) == 13 else barcode[0]
		if prefix == '0': prefix = 'frozen'
		elif prefix == '1': prefix = 'fridge'
		elif prefix == '2': prefix = 'normal'
		
		try:
			sql = "SELECT * FROM items." + prefix + " WHERE " + target + "=" + barcode
			cur.execute(sql)
			res = cur.fetchall()
			if res == []:
				socketio.emit('FindItemByBarcode_Result'+session["usernameHash"],'Invalid barcode')
			else:
				res = {
					# "ean13":res[0][0],  #前端好像沒用到
					# "number":res[0][1], #前端好像沒用到
					"name":res[0][2],
					"price":res[0][3],
					"discount":eval(res[0][4]) if res[0][4] != None else ""
				}
				socketio.emit('FindItemByBarcode_Result'+session["usernameHash"],json.dumps(res))
		except:
			status = connect_mariadb()
			if status == "ok":
				FindItemByBarcode(barcode)
			else:
				socketio.emit('FindItemByBarcode_Result'+session["usernameHash"],"DB Reconnecting")

@socketio.on('FindMemberByPhoneNumber')
def FindMemberByPhoneNumber(phoneNumber):
	if not phoneNumber.isdigit():
		socketio.emit('FindMemberByPhoneNumber_Result'+session["usernameHash"],'Invalid phoneNumber')
	else:
		try:
			prefix = phoneNumber[:3] if len(phoneNumber) == 10 else "09" + phoneNumber[0]
			sql = "SELECT * FROM members.prefix_" + prefix + " WHERE phonenumber LIKE '%" + phoneNumber + "'"
			connect_mariadb()
			cur.execute(sql)
			res = cur.fetchall()
			if res == []:
				socketio.emit('FindMemberByPhoneNumber_Result'+session["usernameHash"],'Invalid phoneNumber')
			else:
				res = {
					"name":res[0][0],
					"phonenumber":res[0][1],
					# "birth":res[0][2],       #前端好像沒用到
					# "reg_date":res[0][3],    #前端好像沒用到
					# "reg_time":res[0][4],    #前端好像沒用到
					# "branch":res[0][5]       #前端好像沒用到
				}
				socketio.emit('FindMemberByPhoneNumber_Result'+session["usernameHash"],json.dumps(res))
		except:
			status = connect_mariadb()
			if status == "ok":
				FindMemberByPhoneNumber(phoneNumber)
			else:
				socketio.emit('FindMemberByPhoneNumber_Result'+session["usernameHash"],"DB Reconnecting")

@socketio.on('newTrade')
def newTrade(name,amount,price,member,payment):
	if type(name) != list or type(amount) != list or type(price) != int or type(payment) != str or (type(member) != bool and type(member) != str): return
	random_code = ""
	for i in range(4):random_code += str(random.randint(0,9))
	try:
		sql = "INSERT INTO trades."+datetime.utcnow().strftime("%Y%m")+"_001_aa (random_code,status,price,items,amount,trade_date,trade_time,member,payment) VALUES(?,?,?,?,?,?,?,?,?)"
		name_str,amount_str = "",""
		for i in range(len(name)):
			name_str += name[i]+","
			amount_str += str(amount[i])+","
		info = (random_code,'success',price,name_str[0:-1],amount_str[0:-1],datetime.utcnow().strftime("%Y-%m-%d"),datetime.utcnow().strftime("%H:%M:%S"),member if member != False else None,payment)
		cur.execute(sql,info)
		connection.commit()
		socketio.emit('newTrade_Result'+session["usernameHash"],'ok')
		if "username" in session.keys():
			print(session["username"])
	except:
		status = connect_mariadb()
		if status == "ok":
			FindItemByBarcode(barcode)
		else:
			socketio.emit('newTrade_Result'+session["usernameHash"],"DB Reconnecting")

if __name__ == '__main__':
	socketio.run(app,host="0.0.0.0",debug=True)