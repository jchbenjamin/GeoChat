package com.eightball.geochat;

import java.util.ArrayList;
 
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;
/*
	Customized adapter representing one entry in ListView UI object
	Used as output by AsyncTask method and XMLParse */
public class CustomAdapter extends BaseAdapter {
    private static ArrayList<Results> resultArrayList;
 
    private LayoutInflater mInflater;
 
    public CustomAdapter(Context context, ArrayList<Results> results) {
        resultArrayList = results;
        mInflater = LayoutInflater.from(context);
    }
 
    public int getCount() {
        return resultArrayList.size();
    }
 
    public Object getItem(int position) {
        return resultArrayList.get(position);
    }
 
    public long getItemId(int position) {
        return position;
    }
 
    public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder holder;
        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.chat_line, null);
            holder = new ViewHolder();
            holder.txtSender = (TextView) convertView.findViewById(R.id.textSender);
            holder.txtLLT = (TextView) convertView.findViewById(R.id.textLLT);
            holder.txtLine = (TextView) convertView.findViewById(R.id.textContent);
 
            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }
 
        holder.txtSender.setText(resultArrayList.get(position).getSender());
        holder.txtLLT.setText(resultArrayList.get(position).getLLT());
        holder.txtLine.setText(resultArrayList.get(position).getLine());
 
        return convertView;
    }
 
    static class ViewHolder {
        TextView txtSender;
        TextView txtLLT;
        TextView txtLine;
    }
}
