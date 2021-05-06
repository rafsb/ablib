import sys
import pandas as pd

from pytrends.request import TrendReq

items = []
date  = "now 7-d"
ofile = "trends.csv"

for block in sys.argv[1:]:
    block = block.split("=")
    if block[0] == "--items":
        items = block[1].replace("'", "").replace('"', '').split(",")
    elif block[0] == "--date":
        date  = block[1]
    elif block[0] == "--output":
        ofile = block[1]

if(len(items)==0):
    print("\n==> --items='first_item,second_item'  <==  É OBRIGATÓRIO!!!\n")
else:
    gt = TrendReq(hl='pt-BR', tz=180)
    gt.build_payload(kw_list=items, cat=0, timeframe=date, geo='BR', gprop='')
    gt.interest_over_time().to_csv(ofile, ";")