# Makefile.head start

# This file is part of the Yate-BTS Project http://www.yatebts.com
#
# Yet Another Telephony Engine - Base Transceiver Station
# Copyright (C) 2013-2014 Null Team Impex SRL
# Copyright (C) 2014 Legba, Inc
#
# This software is distributed under multiple licenses;
# see the COPYING file in the main directory for licensing
# information for this specific distribution.
#
# This use of this software may be subject to additional restrictions.
# See the LEGAL file in the main directory for details.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

TOP_INCLUDES := -I../.. -I../.. -I.
GSM_INCLUDES := $(TOP_INCLUDES) -I./../CommonLibs -I./../A53 \
    -I./../Connection -I./../GSM -I./../Globals -I./../sqlite3
ALL_INCLUDES := $(GSM_INCLUDES) -I./../Control -I./../CLI -I./../GPRS \
    -I./../Peering -I./../SGSNGGSN -I./../TRXManager

SQL_LIBS := -L../sqlite3 -lsqlite3 -lpthread		# Hack to run RangeNetworks OpenBTS v5 using the Nuand BladeRF x40 #
GSM_LIBS := -L../GSM -lGSM -L../Connection -lConnection -L../Peering -lPeering \
    -L../CommonLibs -lCommonLibs -L../Globals -lGlobals $(SQL_LIBS)
ALL_LIBS := -L../CLI -lCLI -L../Control -lControl -L../GPRS -lGPRS \
    -L../SGSNGGSN -lSGSNGGSN -L../TRXManager -lTRXManager $(GSM_LIBS)
A53_LIBS := -L../A53 -lA53

SQL_DEPS := ../sqlite3/libsqlite3.a
GSM_DEPS := $(SQL_DEPS) ../CommonLibs/libCommonLibs.a ../Connection/libConnection.a \
    ../GSM/libGSM.a ../Globals/libGlobals.a
ALL_DEPS := $(GSM_DEPS) ../CLI/libCLI.a ../Control/libControl.a ../GPRS/libGPRS.a \
    ../Peering/libPeering.a ../SGSNGGSN/libSGSNGGSN.a ../SGSNGGSN/libSGSNGGSN.a \
    ../TRXManager/libTRXManager.a
A53_DEPS := ../A53/libA53.a

prefix = /usr/local
exec_prefix = ${prefix}
moddir := 

DEBUG :=
CXX  := g++ -Wall
AR  := ar
DEFS :=
OBJS :=
LIBS :=
PROGS:=
FILES:=
LIBTHR := 
CCFLAGS:= 
CFLAGS :=$(subst -Wno-overloaded-virtual,,$(CCFLAGS))
LDFLAGS:= 

# Makefile.head end

# Makefile
# This file holds the make rules for the RAD1 transceiver

INCLUDES := $(GSM_INCLUDES)
LIBDEPS  := $(GSM_DEPS)
INCFILES := ad9862.h bytesex.h commands.h Complex.h DummyLoad.h \
    fpga_regs.h fusb.h i2c.h ids.h interfaces.h RAD1Device.h radioDevice.h \
    radioInterface.h rnrad1Core.h rnrad1.h sigProcLib.h spi.h Transceiver.h
LOCALLIBS = $(GSM_LIBS)

ifeq ($(BUILD_TESTS),yes)
PROGS := sigProcLibTest
endif

PROGS_RAD1 := transceiver-rad1
ifneq (yes,no)
FILES:= $(FILES) ezusb.ihx fpga.rbf
ifeq ($(BUILD_TESTS),yes)
PROGS_RAD1 := $(PROGS_RAD1) RAD1ping RAD1RxRawPower RAD1RxRawPowerSweep RAD1Cmd RAD1SN
endif
PROGS:= $(PROGS) $(PROGS_RAD1)
endif

PROGS_BRF := transceiver-bladerf
ifneq (yes,no)
FILES:= $(FILES) firmware.img hostedx40.rbf hostedx115.rbf
PROGS:= $(PROGS) $(PROGS_BRF)
endif

LIBS := libtransceiver.a
OBJS := DummyLoad.o fusb.o radioInterface.o \
    rnrad1Core.o rnrad1Rx.o rnrad1Tx.o sigProcLib.o Transceiver.o
EXTRACLEAN := runTransceiver.o

all:

$(PROGS_RAD1): LOCALLIBS = $(GSM_LIBS) -lusb-1.0
$(PROGS_RAD1): LOCALFLAGS = 

transceiver-rad1: RAD1Device.cpp RAD1Device.h runTransceiver.o $(MKDEPS) $(INCFILES) $(LIBS) $(LIBDEPS)
	$(COMPILE) -o $@ $(LOCALFLAGS) $< runTransceiver.o $(LIBS) $(LIBTHR) $(LDFLAGS) $(LOCALLIBS)

$(PROGS_BRF): LOCALLIBS = $(GSM_LIBS) -lbladeRF
$(PROGS_BRF): LOCALFLAGS = 

transceiver-bladerf: bladeRFDevice.cpp bladeRFDevice.h runTransceiver.o $(MKDEPS) $(INCFILES) $(LIBS) $(LIBDEPS)
	$(COMPILE) -o $@ $(LOCALFLAGS) $< runTransceiver.o $(LIBS) $(LIBTHR) $(LDFLAGS) $(LOCALLIBS)

# Makefile.tail start

COMPILE = $(CXX) $(DEFS) $(DEBUG) $(INCLUDES) $(CCFLAGS)
CCOMPILE = $(CC) -std=gnu99 $(CFLAGS) $(DEFS) $(DEBUG) $(INCLUDES)
LINK = $(CXX) $(LDFLAGS)

# include optional local make rules
-include YateLocal.mak

.PHONY: all debug ddebug xdebug tests
all: $(PROGS) $(LIBS)

debug:
	$(MAKE) all DEBUG=-g3 MODSTRIP=

ddebug:
	$(MAKE) all DEBUG='-g3 -DDEBUG' MODSTRIP=

xdebug:
	$(MAKE) all DEBUG='-g3 -DXDEBUG' MODSTRIP=

tests:
	$(MAKE) all BUILD_TESTS=yes

.PHONY: strip clean install uninstall
strip: all
	strip --strip-debug --discard-locals $(PROGS)

clean:
	@-$(RM) $(PROGS) $(LIBS) $(OBJS) $(EXTRACLEAN) core 2>/dev/null

install: $(PROGS)
	@mkdir -p "$(DESTDIR)$(moddir)/server/bts" && \
	for i in $(PROGS) ; do \
	    install -D "$$i" "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done
	for i in $(FILES) ; do \
	    install -m 0644 "$$i" "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done

uninstall:
	@-for i in $(FILES) ; do \
	    rm -f "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done
	@-for i in $(PROGS) ; do \
	    rm -f "$(DESTDIR)$(moddir)/server/bts/$$i" ; \
	done
	@-test -d "$(DESTDIR)$(moddir)/server/bts" && rmdir "$(DESTDIR)$(moddir)/server/bts"

%.o: ./%.cpp $(INCFILES)
	$(COMPILE) -c $<

%.o: ./%.c $(INCFILES)
	$(CCOMPILE) -c $<

Makefile: ./Makefile.in ../../config.status
	cd ../.. && ./config.status

$(LIBS): $(OBJS)
	$(AR) rcs $@ $^

%: %.cpp $(MKDEPS) $(INCFILES) $(LIBS) $(LIBDEPS)
	$(COMPILE) -o $@ $(LOCALFLAGS) $< $(LIBS) $(LIBTHR) $(LDFLAGS) $(LOCALLIBS)

../%.a:
	$(MAKE) -C `dirname $@`

# Makefile.tail end

