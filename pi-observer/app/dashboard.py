import mysql.connector
import dash
import dash_html_components as html
import dash_bootstrap_components as dbc
import pandas as pd

from app import db_connection
from app.components import make_graph_table_pair, make_map_table_pair, make_graph_bundled_tables, wrap_in_card


def load_data(db_connection, table_names):
    sql_to_df = lambda log_name: pd.read_sql('select * from ' +
        log_name, db_connection, index_col='num')
    return {name:sql_to_df(name) for name in table_names}


def make_layout(data):
    labels = ('Temperature', 'Pressure', 'Altitude', 'Humidity')
    logs = [
        make_graph_bundled_tables([data['temp_log'], data['int_temp_log']],
                                  ['internal', 'external']),
        make_graph_table_pair(data['press_log']),
        make_graph_table_pair(data['alt_log']),
        make_graph_table_pair(data['hum_log']),
    ]
    tabs = [dbc.Tab(wrap_in_card(log), label=label) for log, label in zip(logs, labels)]
    tab_bar = dbc.Tabs(tabs)

    return html.Div(className="container", children=[
        dbc.Alert('Welcome to the logs dashboard.', color='primary', className='mt-3'),
        dbc.Card(
            [
                dbc.CardHeader(html.H4('Location log', style={'margin': '0'})),
                dbc.CardBody([make_map_table_pair(data['loc_log'])]),
            ],
            className="my-3"
        ),
        html.H4('Sensor logs'),
        tab_bar
    ])


def make_dash(server):
    external_stylesheets = [dbc.themes.BOOTSTRAP]
    app = dash.Dash(
        __name__,
        server=server,
        url_base_pathname='/dash/',
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

    return app
