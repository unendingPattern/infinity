#!/bin/sh

echo -n '+ <a href="https://github.com/ctrlcctrlv/infinity/">infinity</a> + <a href="https://github.com/czaks/8chan/">8ch.pl</a> ' > .installed
git describe --long --tags >> .installed


