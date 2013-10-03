package com.eightball.geochat;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

public class MySQLiteHelper extends SQLiteOpenHelper {

/* table layout of incoming message
 * CREATE TABLE Messages(
        messageId       SERIAL NOT NULL PRIMARY KEY,
        senderId        numeric(5,0) REFERENCES Users(userId),
        message         varchar(300) NOT NULL,
        location        GEOMETRY(point),
        radius          numeric(2,0),
        time            TIMESTAMP NOT NULL
);
stored in sqlite as
	id		INTEGER
	sender		TEXT
	message		TEXT
	llt		TEXT
*/	

  private static final String DATABASE_NAME = "chat.db";
  private static final int DATABASE_VERSION = 1;

  // Database creation sql statement
  private static final String DATABASE_CREATE = "CREATE TABLE messages( id INTEGER PRIMARY KEY, sender TEXT NOT NULL, message TEXT NOT NULL, llt TEXT NOT NULL );";

  public MySQLiteHelper(Context context) {
    super(context, DATABASE_NAME, null, DATABASE_VERSION);
  }

  @Override
  public void onCreate(SQLiteDatabase database) {
    database.execSQL(DATABASE_CREATE);
  }

  @Override
  public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
    Log.w(MySQLiteHelper.class.getName(),
        "Upgrading database from version " + oldVersion + " to "
            + newVersion + ", which will destroy all old data");
    db.execSQL("DROP TABLE IF EXISTS messages");
    onCreate(db);
  }

} 