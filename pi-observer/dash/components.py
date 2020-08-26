import dash_core_components as dcc
import plotly.express as px
import dash_table
import pandas as pd
import plotly.graph_objects as go

def make_table(df):
    table = dash_table.DataTable(
        columns=[{'name': i, 'id': i} for i in df.columns],
        data=df.to_dict('records'),
        style_table={
            'height': 500,
            'overflowY': 'auto',
        },
    )
    return table


def make_graph_bundled_tables(dfs, labels):
    fig = go.Figure()
    for df, label in zip(dfs, labels):
        x= df['time'].to_numpy()
        y = df['value'].to_numpy()
        fig.add_trace(go.Scatter(x=x, y=y, name = label))

    graph = dcc.Graph(figure=fig)
    tables = [make_table(df) for df in dfs]
    return (graph, *tables)


def make_graph_table_pair(df):
    fig = px.line(df, x='time', y='value')
    graph = dcc.Graph(figure=fig)
    table = make_table(df)
    return (graph, table)


def make_map_table_pair(df):
    fig = px.line_mapbox(df, lat='latitude', lon='longitude')
    fig.update_layout(mapbox_style='carto-positron')
    graph = dcc.Graph(figure=fig)
    table = make_table(df)
    return (graph, table)
