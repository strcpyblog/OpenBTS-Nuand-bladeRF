/*
* Copyright 2008 Free Software Foundation, Inc.
* Copyright 2014 Range Networks, Inc.
*
*
* This software is distributed under the terms of the GNU Affero Public License.
* See the COPYING file in the main directory for details.
*
* This use of this software may be subject to additional restrictions.
* See the LEGAL file in the main directory for details.

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

#define ENABLE_VECTORDEBUG

#include "Vector.h"
#include <iostream>

// We must have a gConfig now to include Vector.
#include "Configuration.h"
ConfigurationTable gConfig;

using namespace std;

typedef Vector<int> TestVector;
int barfo;
void foo(TestVector a)
{
	barfo = a.size();	// Do something so foo wont be optimized out.
}
void anotherTest()
{
	cout << "START Vector anotherTest" << endl;
	TestVector v0(10);
	TestVector atest = v0.head(3);
	cout << atest << endl;
	cout << "calling head" << endl;
	cout << v0.head(3) << endl;
	cout << "Passing Vector" << endl;
	// This calls the Vector non-const copy constructor
	foo(v0);
	cout << "FINISH anotherTest" << endl;
}

int main(int argc, char *argv[])
{
	anotherTest();
	TestVector test1(5);
	for (int i=0; i<5; i++) test1[i]=i;
	TestVector test2(5);
	for (int i=0; i<5; i++) test2[i]=10+i;

	cout << test1 << endl;
	cout << test2 << endl;

	{
		TestVector testC(test1,test2);
		cout << testC << endl;

		TestVector foo = testC.head(3);
		//cout << testC.head(3) << endl;
		cout << testC.tail(3) << endl;
		testC.fill(8);
		cout << testC << endl;
		test1.copyToSegment(testC,3);
		cout << testC << endl;

		TestVector testD(testC.segment(4,3));
		cout << testD << endl;
		testD.fill(9);
		cout << testC << endl;
		cout << testD << endl;
	}

	return 0;
}
