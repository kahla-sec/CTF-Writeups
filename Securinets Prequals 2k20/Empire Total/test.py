import mysql.connector
import time
import os
Host="localhost"
UsernameR="kahla"
PasswordR="123"
Database="chall3"
CursorR=None
DBR = mysql.connector.connect(
  host=Host,
  user=UsernameR,
  passwd=PasswordR,
  database=Database
  )
CursorR = DBR.cursor(dictionary=True)

def persist():
  table_name="users"
  time="15"
  last="yes"
  injection="100.26.206.184/?u=',(select 1),(select 2)),('99','a"
  try:
    CursorR.execute("INSERT INTO "+table_name+" (id,username,password,test) VALUES ('"+str(10)+"','"+injection+"','"+time+"','"+last+"')")
    print("INSERT INTO "+table_name+" (id,username,password,test) VALUES ('"+str(2)+"','"+injection+"','"+time+"','"+last+"')")
    DBR.commit()
    #resetSQL()
  except Exception as e:
    print("INSERT INTO "+table_name+" (id,username,password,test) VALUES ('"+str(2)+"','"+injection+"','"+time+"','"+last+"')")

#    print("INSERT INTO "+table_name+" (id,username,password) VALUES ('"+"15"+"','"+domain['Domain']+"','"+domain['Date resolved']+"')")
    print("EXCEPTION: ",e)
    #resetSQL()
#initSQL()
persist()