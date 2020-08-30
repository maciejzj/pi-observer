import mysql.connector
import dash
import dash_html_components as html
import dash_bootstrap_components as dbc
import pandas as pd

import db_connection
from components import make_graph_table_pair, make_map_table_pair, make_graph_bundled_tables, wrap_in_card


def load_data(db_connection, table_names):
    sql_to_df = lambda log_name: pd.read_sql('select * from ' +
        log_name, db_connection, index_col='num')
    return {name:sql_to_df(name) for name in table_names}


def make_layout(data):
    return html.Div(children=[
        html.Div(className='container', children=[
        wrap_in_card(make_map_table_pair(data['loc_log']), 'Location log'),
        wrap_in_card(make_graph_bundled_tables([data['temp_log'], data['int_temp_log']],
                                   ['internal', 'external']), 'Temperature log'),
        wrap_in_card(make_graph_table_pair(data['press_log']), 'Pressure log'),
        wrap_in_card(make_graph_table_pair(data['alt_log']), 'Altitude log'),
        wrap_in_card(make_graph_table_pair(data['hum_log']), 'Humidity log'),
        ])
    ])


if __name__ == '__main__':
    external_stylesheets = ['https://codepen.io/chriddyp/pen/bWLwgP.css', dbc.themes.BOOTSTRAP]
    app = dash.Dash(
        __name__,
        external_stylesheets=external_stylesheets,
        meta_tags=[{
            "name": "viewport",
            "content": "width=device-width, initial-scale=1"
        }]
    )

    table_names = ('temp_log', 'int_temp_log', 'press_log', 'alt_log', 'hum_log', 'loc_log')
    data_db = mysql.connector.connect(**db_connection.data_logs_db)
    data = load_data(data_db, table_names)

    app.layout = make_layout(data)

    app.run_server(debug=True, host='0.0.0.0')
