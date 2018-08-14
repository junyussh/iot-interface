import sys
from uuid import getnode as get_mac
import MySQLdb

def main():
    arg = sys.argv[1]
    if(arg == "MAC"):
        mac = get_mac()
        mac = ':'.join(("%012X" % mac)[i:i+2] for i in range(0, 12, 2))
        print(mac)
    elif (arg == "Name"):
        db = MySQLdb.connect(host="localhost",
        user="root", passwd="root", db="jgate_modbus")
        cursor = db.cursor()
        cursor.execute("SELECT * FROM device_info;")
        results = cursor.fetchall()
        print(results[0][4])
    elif (arg == "domain"):
        db = MySQLdb.connect(host="localhost",
        user="root", passwd="root", db="jgate_modbus")
        cursor = db.cursor()
        cursor.execute("SELECT * FROM cloud_setting;")
        results = cursor.fetchone()
        print(results[2])
if __name__ == "__main__":
    main()