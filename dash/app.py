import mysql.connector
import dash
import dash_html_components as html
import pandas as pd

import db_connection
from components import make_graph_table_pair, make_map_table_pair, make_graph_bundled_tables

def load_data(db_connection, table_names):
    sql_to_df = lambda log_name: pd.read_sql('select * from ' +
        log_name, db_connection, index_col='num')
    return {name:sql_to_df(name) for name in table_names}


def make_layout(data):
    return html.Div(children=[
        html.H1(children='Hello Dash'),
        html.Div(children='''
            Dash: A web application framework for Python.
        '''),

        *make_map_table_pair(data['loc_log']),
        *make_graph_bundled_tables([data['temp_log'], data['int_temp_log']],
                                   ['inter', 'exter']),
        *make_graph_table_pair(data['press_log']),
        *make_graph_table_pair(data['alt_log']),
        *make_graph_table_pair(data['hum_log']),
    ])


if __name__ == '__main__':
    external_stylesheets = ['https://codepen.io/chriddyp/pen/bWLwgP.css']
    app = dash.Dash(__name__, external_stylesheets=external_stylesheets)

    table_names = ('temp_log', 'int_temp_log', 'press_log', 'alt_log', 'hum_log', 'loc_log')
    data_db = mysql.connector.connect(**db_connection.data_logs_db)
    data = load_data(data_db, table_names)

    app.layout = make_layout(data)

    app.run_server(debug=True, host='0.0.0.0')
