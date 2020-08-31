import dash_core_components as dcc
import dash_html_components as html
import plotly.express as px
import dash_table
import dash_bootstrap_components as dbc
import pandas as pd
import plotly.graph_objects as go


def make_table(df):
    table = dash_table.DataTable(
        css=[{'selector': '.row', 'rule': 'margin: 0'}],
        columns=[{'name': i, 'id': i} for i in df.columns],
        data=df.to_dict('records'),
        page_size=12,
        style_table={
            'height': 450,
            'overflowY': 'auto',
        },
    )
    return table


def wrap_in_card(div):
    card = dbc.Card(
        [
            dbc.CardBody([div]),
        ],
        style={'margin-top': '1rem', 'margin-bottom': '1rem'}
    )
    return card


def make_graph_bundled_tables(dfs, labels):
    fig = go.Figure()
    for df, label in zip(dfs, labels):
        x= df['time'].to_numpy()
        y = df['value'].to_numpy()
        fig.add_trace(go.Scatter(x=x, y=y, name=label))

    graph = dcc.Graph(figure=fig)
    table_divs = [dbc.Col(make_table(df), xs=12, lg=6) for df in dfs]

    div = html.Div(children=[graph, dbc.Row(table_divs)])
    return div


def make_graph_table_pair(df):
    fig = px.line(df, x='time', y='value')
    graph = dcc.Graph(figure=fig, config={'displayModeBar': True})
    table = make_table(df)

    return dbc.Row([dbc.Col(graph, xs=12, lg=8), dbc.Col(table)]) 


def make_map_table_pair(df):
    to_map = df[df['status'] == 'Correct']
    fig = px.line_mapbox(to_map, lat='latitude', lon='longitude')
    fig.update_layout(mapbox_style='carto-positron')
    graph = dcc.Graph(figure=fig)
    table = make_table(df)
    return html.Div(children=[graph, table])
