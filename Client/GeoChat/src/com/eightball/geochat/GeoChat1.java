package com.eightball.geochat;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Vector;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import org.apache.http.HttpEntity;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.AsyncTask;
import android.os.Bundle;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Spinner;




public class GeoChat1 extends Activity {
	
	public static final String TAG = "GeoChat1";
	//unimplemented spinner for setting radius - Totally complicated!
	private Spinner spinner;
	//ListView object
	private ListView listView;
	//Objects for putting entries into listView
	private CustomAdapter listAdapter;
	private ArrayList<Results> arrayList;
	//Basic URL of our server site - GET request built on top of this with StringBuilder objects
	private String baseUrlString = new String("http://babbage.missouri.edu/~cs3380sp13grp8/new.php");
	private HttpURLConnection urlConnection = null;
	//temporary username and pass
	//String user = "jb";
	//String pass = "jb";
	//radius
	int rad = 5;
	
	//geolocation coords
	double lat;
	double lon;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_geo_chat1);
		
		//CODE FOR GETTING USERNAME/PASS FROM SETTINGS - abandoned second Activity
		//Intent intentFromSettings = getIntent();
	    //user = intent.getStringExtra(Settings.USER_MESG);
		
		//***INITIALIZE THE ENVIRONMENT ***
		
		//SET UP MAIN CHATLISTVIEW
	    // Find the ListView resource. 
	    listView = (ListView) findViewById( R.id.chatListView );
	    // Set up ArrayList which is data structure for chat lines.
	    //String[] tempfill = new String[] { "first line", "second line" };
	    arrayList = new ArrayList<Results>();
	    //arrayList.addAll( Arrays.asList(tempfill) );
	    // Create ArrayAdapter using underlying ArrayList
	    listAdapter = new CustomAdapter(this, arrayList);
	    // Set the ArrayAdapter as the ListView's adapter.
	    listView.setAdapter( listAdapter );  
	 
	     
	    
//set up location listener
	 // Acquire a reference to the system Location Manager
	    LocationManager locationManager = (LocationManager) this.getSystemService(Context.LOCATION_SERVICE);

	    // Define a listener that responds to location updates
	    LocationListener locationListener = new LocationListener() {
	        public void onLocationChanged(Location location) {
	          // Called when a new location is found by the network location provider.
	          lon = location.getLongitude();
	          lat = location.getLatitude();
	        }

	        public void onStatusChanged(String provider, int status, Bundle extras) {}

	        public void onProviderEnabled(String provider) {}

	        public void onProviderDisabled(String provider) {}
	      };

	    // Register the listener with the Location Manager to receive location updates
	    locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0, 0, locationListener);
	     
	  }
	
	  public void pressSendButton(View view) {

		  	//Results r = new Results();
		  	
			EditText editText = (EditText) findViewById(R.id.userEditText);
			EditText editRad = (EditText) findViewById(R.id.userEditRad);
			EditText editUser = (EditText) findViewById(R.id.userEditUser);
			EditText editPass = (EditText) findViewById(R.id.userEditPass);
			
			String user = editUser.getText().toString();
			String pass = editPass.getText().toString();
			rad = Integer.parseInt(editRad.getText().toString());
			//URL myUrl = null;
			
			  StringBuilder urlString = new StringBuilder(baseUrlString);
			  urlString.append("?mode=message&lat=" + Double.toString(lat) + "&lon=" + Double.toString(lon));
			urlString.append("&user=" + user + "&pass=" + pass);
			urlString.append("&rad=" + Integer.toString(rad) + "&mesg=" + editText.getText().toString());
			  
			/*try {
				myUrl = new URL(urlString.toString());
			} catch (MalformedURLException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}*/
			
			new HttpGetter().execute(urlString.toString());
			//arrayList now has new lines!
			
			/*String latlongtest = new String("debug: lon" + Double.toString(lon) + " |lat" + Double.toString(lat) + " |user:" + user + " |pass:" + pass + " |rad:" + Integer.toString(rad));
			arrayList.add(latlongtest);*/
			listAdapter.notifyDataSetChanged();
			
			editText.setText("");
			editText.clearFocus();
		  }
	
	  @Override
	  public boolean onOptionsItemSelected(MenuItem item) {
	      // Handle item selection
	      switch (item.getItemId()) {
	          case R.id.menu_refresh:
	              pressRefresh();
	              return true;

	          default:
	              return super.onOptionsItemSelected(item);
	      }
	  }

	  
	  public void pressRefresh() {
		//Similar to Send Button action, except radius and message input ignored and not sent to server
		EditText editUser = (EditText) findViewById(R.id.userEditUser);
		EditText editPass = (EditText) findViewById(R.id.userEditPass);
			
		String user = editUser.getText().toString();
		String pass = editPass.getText().toString();
			
		StringBuilder urlString = new StringBuilder(baseUrlString);
		urlString.append("?mode=update&lat=" + Double.toString(lat) + "&lon=" + Double.toString(lon));
		urlString.append("&user=" + user + "&pass=" + pass);
			
		new HttpGetter().execute(urlString.toString());
		listAdapter.notifyDataSetChanged();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_geo_chat1, menu);
		return true;
	}

/*AsyncTask performs network and parsing activity off UI thread*/
private class HttpGetter extends AsyncTask<String, Void, ArrayList<Results>> {
        @Override
       protected ArrayList<Results> doInBackground(String... address) {
        	URL url = null;
        	HttpURLConnection urlConnection = null;
        	InputStream in = null;
            ArrayList<Results> myReturnList = null;
    	    try {
				//address[0] taken as first input argument
				//in our case this is always the URL formatted with GET request
    	    	url = new URL(address[0]);
    		    urlConnection = (HttpURLConnection) url.openConnection();
    	      in = new BufferedInputStream(urlConnection.getInputStream());
    	      //Returned ArrayList formatted from Parser
    	      myReturnList = XmlParse(in);
    	      
    	    } catch (Exception e) {
    	    		//Log.d()
    	    }
    	     finally {
    	      urlConnection.disconnect();
    	      return(myReturnList);
    	    }       
              
        }
        //Post results to arrayList global variable.
        @Override
        protected void onPostExecute(ArrayList<Results> result) {
        	arrayList.addAll(result); //append ArrayList to ArrayList
        }
}


//METHOD TO PARSE XML

//private ArrayList<Results> XmlParse (InputStream in)
public ArrayList<Results> XmlParse(InputStream in) 
{

	ArrayList myReturn = new ArrayList<Results>();
	try
	{
	       //File XMLFile = new File("test2.xml");
		DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder dBuilder = dbFactory.newDocumentBuilder();
		Document doc = dBuilder.parse(in);

		doc.getDocumentElement().normalize();

	       //System.out.println("Top Level Tag :" + doc.getDocumentElement().getNodeName());
		NodeList nList = doc.getElementsByTagName("message");
	      
	       //System.out.println("--- --- --- --- --- --- --- --- --- ---");

		for (int temp = 0; temp < nList.getLength(); temp++)
		{
			Results r = new Results();
			StringBuilder lltString = new StringBuilder(); 
			Node nNode = nList.item(temp);
	    
			//thisString.append("\nCurrent Element : " + nNode.getNodeName());
	    
			if (nNode.getNodeType() == Node.ELEMENT_NODE)
			{
	    
				Element eElement = (Element) nNode;
				/*
	                       //thisString.append("----------------------------");System.out.println("Message ID : " + eElement.getAttribute("id"));
	                       thisString.append("[" + eElement.getElementsByTagName("username").item(0).getTextContent() + "]");
	                       thisString.append(": " + eElement.getElementsByTagName("content").item(0).getTextContent());
	                       thisString.append(" @" + eElement.getElementsByTagName("time").item(0).getTextContent());
	                       thisString.append(" [l]: " + eElement.getElementsByTagName("loc").item(0).getTextContent());
	                       thisString.append(" [r]: " + eElement.getElementsByTagName("radius").item(0).getTextContent());
	                       //System.out.println("----------------------------");
	              OLD CODE FOR WHEN WE WERE PASSING JUST A STRING
	                        */
					r.setSender(eElement.getElementsByTagName("username").item(0).getTextContent());
					//build LLT line
                    lltString.append(eElement.getElementsByTagName("time").item(0).getTextContent());
                    lltString.append(", " + eElement.getElementsByTagName("loc").item(0).getTextContent());
                    lltString.append(", " + eElement.getElementsByTagName("radius").item(0).getTextContent() + "m");
					r.setLLT(lltString.toString());
					r.setLine(eElement.getElementsByTagName("content").item(0).getTextContent());
					myReturn.add(r);
			}
				
		}
	}
	catch (Exception e)
	{
	       e.printStackTrace();
	}
	finally
	{
		   
	}
	return myReturn;
	}
}		


