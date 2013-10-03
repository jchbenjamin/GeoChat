package com.eightball.geochat;

/*
 * Information holding object for Adapter
 */
public class Results {
     private String sender = "";
     private String latlongtime = "";
     private String line = "";
 
     public void setSender(String sender) {
      this.sender = sender;
     }
 
     public String getSender() {
      return sender;
     }
 
     public void setLLT(String latlongtime) {
    	 this.latlongtime = latlongtime;
     }
     
     public String getLLT() {
    	 return latlongtime;
     }
     public void setLine(String line) {
      this.line = line;
     }
 
     public String getLine() {
      return line;
     }

}
